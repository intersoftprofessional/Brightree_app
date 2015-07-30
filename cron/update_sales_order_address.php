<?php
require_once("class/class.brighttreesalesorderservice.php");
require_once('class/USPSAddressVerify.php');
require_once('class/connection.php');


$startdate = date('Y-m-d', strtotime("-1 days"));
//$startdate = '2015-07-08';
$enddate = date('Y-m-d');
//$enddate = '2015-07-10';

//Initiate and set the username password provided from brighttree
// $obj = new BrighttreeSalesOrderService("https://webservices.brightree.net/v0100-1501/OrderEntryService/SalesOrderService.svc","apiuser@GenevaWoodsSBX","gw2015!!");
$obj = new BrighttreeSalesOrderService("https://webservices.brightree.net/v0100-1501/OrderEntryService/SalesOrderService.svc","shaeva@chspharmapitest","coffee4u");


$result = $obj->SalesOrderSearch($startdate,$enddate);

$xml = simplexml_load_string((string) $result);

$totalRecords = $xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->TotalItemCount;
$records =$xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->Items->children('b',true)->SalesOrderSearchResponse;


if($records && ( count($records) > 0)) {
	echo '<table border="1"><tr><th>Sales Order Brightree ID</th><th>Patient Name</th><th>Status</th><th>Results</th></tr>';
	foreach($records as $key => $record)
	{
		//echo '<pre>'.print_r($record, true).'</pre>';
		//get brightreeID
		$BrightreeID = (string) $record->children('b',true)->BrightreeID;
		
		//Get object of patient from brightree 
		$sales_order = $obj->SalesOrderFetchByBrightreeID($BrightreeID);
		
		$xml = simplexml_load_string((string) $sales_order);

		$sales_order =$xml->children('s',true)->children()->SalesOrderFetchByBrightreeIDResponse->children()->SalesOrderFetchByBrightreeIDResult
				->children('a',true)->Items->children('b',true)->SalesOrder;
				
		$Address = $sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true); 
		
		//get sales order information
		$sales_order_status = (string) $record->children('b',true)->Status;
		$sales_order_patient = (string) $record->children('b',true)->Patient->children('c',true)->Value;
		$sales_order_CreateDate = (string) $record->children('b',true)->CreateDate;
		
		
		//get delivery address of patient
		$AddressLine1= (string) $Address->AddressLine1;
		$AddressLine2= (string) $Address->AddressLine2;
		$AddressLine3= (string) $Address->AddressLine3;
		$City= (string) $Address->City;
		$Country= (string) $Address->Country;
		$County= (string) $Address->County;
		$PostalCode= (string) $Address->PostalCode;
		$State= (string) $Address->State;
		
		//get taxzone of sales order
		$taxZoneID = $sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID; 
		$taxZoneValue = $sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->Value;
		
		//Initiate and set the username provided from usps
		$verify = new USPSAddressVerify('272REACH6842');

		// Create new address object and assign the properties
		// apartently the order you assign them is important so make sure
		// to set them as the example below
		$address = new USPSAddress;
		$address->setFirmName('');
		$address->setApt(trim($AddressLine2));
		$address->setAddress(trim($AddressLine1));
		$address->setCity(trim($City));
		$address->setState(trim($State));
		
		if(strlen(trim($PostalCode)) > 4){ 
			//if postal code is greater then 4 then send first 5 char in Zip5
			$address->setZip5(substr(trim($PostalCode), 0, 5));
			$address->setZip4('');
		}else{
			//else send first 4 in Zip4
			$address->setZip5('');
			$address->setZip4(trim($PostalCode));
		}
		

		// Add the address object to the address verify class
		$verify->addAddress($address);

		// Perform the request and return result
		$verify->verify();

		$correctAddress= $verify->getArrayResponse();
		
		if($verify->isSuccess()) {	
			
			//Now Update the correct deliveryAddress in the sales order object		
				
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine1 = (isset($correctAddress['AddressValidateResponse']['Address']['Address2'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address2']) : '';				
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine2 = (isset($correctAddress['AddressValidateResponse']['Address']['Address1'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address1']) : '';				
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->City = trim($correctAddress['AddressValidateResponse']['Address']['City']);								
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->PostalCode =trim($correctAddress['AddressValidateResponse']['Address']['Zip5']);				
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->State =trim($correctAddress['AddressValidateResponse']['Address']['State']);			
				$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID = '2';
			
				//remove nil from elements
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine1->attributes('i',true)->nil);														
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine2->attributes('i',true)->nil);														
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->City->attributes('i',true)->nil);														
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->PostalCode->attributes('i',true)->nil);														
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->State->attributes('i',true)->nil);
				unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID->attributes('i',true)->nil);					
											
				if($County || ($State && strtolower($State) != "washington")) {
					//if county exist or state of patient is not washington							
					if(! $County){
						//if county is nil then add state as county to search taxzones
						$County=$State;
					}
					
					//look for county in database				
					$result = mysql_query('select taxzone_ID from county_taxzone_mapping where LOWER( county_taxzone_mapping.county ) = "'.strtolower($County).'" AND published="1"');
					
					if(! mysql_num_rows($result)) {
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->attributes('i',true)->nil);
						//set TaxZone 999 with id 9 if does not exist in the database
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID = '369';
					}else{
						if(mysql_num_rows($result) > 1) {
							//multiple records then update 999 taxzone
							$UpdatedTaxZone = '369';
						}else{
							//If single record then update it
							$value = mysql_fetch_object($result);
							$UpdatedTaxZone = $value->taxzone_ID;							
						}
					
					
						//update taxzone value from database correspond to existing county
						if(isset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID)){
								//update value if already set									
								$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID = ((string) $UpdatedTaxZone);
						}else{
									//add element if not set
									unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->attributes('i',true)->nil);	
									$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->addChild('c:ID',((string) $UpdatedTaxZone),'http://schemas.datacontract.org/2004/07/Brightree.ExternalAPI.CanonicalObjects.Common');
						}
					}
				}				
				
				//$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->Value = 'New Tax Zone';
								
				$SalesOrderObjXML= str_replace(
					array("<b:SalesOrder>","</b:SalesOrder>"),
					array("<SalesOrder>", "</SalesOrder>"),
					$sales_order->asXML()
				);
				
				
				
				// Update sales order object on brighttree			
				$resultxml = simplexml_load_string((string) $obj->SalesOrderUpdate($BrightreeID,$SalesOrderObjXML));	
				
				//show result				
				if( (bool) $resultxml->children('s',true)->children()->SalesOrderUpdateResponse->children()->SalesOrderUpdateResult->children('a',true)->Success)
				{
					echo "<td>$BrightreeID</td><td>$sales_order_patient</td><td>$sales_order_status</td><td>Updated successfully</td>";
				}else{
					echo "<td>$BrightreeID</td><td>$sales_order_patient</td><td>$sales_order_status</td><td>".$resultxml->children('s',true)->children()->PatientUpdateResponse->children()->PatientUpdateResult->children('a',true)->Messages."</td>";					
				}
				
		}else {
		  //nill sales order county if address is not verified
		  $sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->County='';		
		  $SalesOrderObjXML= str_replace(
			  array("<b:SalesOrder>","</b:SalesOrder>"),
			  array("<SalesOrder>", "</SalesOrder>"),
			  $sales_order->asXML()
		  );
		
		  // Update sales order object on brighttree			
		  $resultxml = simplexml_load_string((string) $obj->SalesOrderUpdate($BrightreeID,$SalesOrderObjXML));
		  echo "<td>$BrightreeID</td><td>$sales_order_patient</td><td>$sales_order_status</td><td>Error : ". $verify->getErrorMessage()."</td>";		  
		}
		echo '</tr>';
		$verify = NULL;		
	}
		echo '</table>';
}else {
	echo 'No Sales Order Created b/w '.$startdate.' to '.$enddate;
}