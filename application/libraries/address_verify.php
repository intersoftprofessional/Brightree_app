<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Address_Verify Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 */
class Isp_Address_Verify {

    var $patientOBJ;
    var $SalesOrderOBJ;

    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */
    public function __construct($params = array()) {
        require_once("class/class.brighttreepatientservice.php");
        require_once("class/class.brighttreesalesorderservice.php");
        require_once('class/USPSAddressVerify.php');
        
		//get instance
		$this->ISP =& get_instance();
        //Initiate and set the username password provided from brighttree
        //$this->patientOBJ = new BrighttreePatientService("https://webservices.brightree.net/v0100-1302/OrderEntryService/PatientService.svc", "apiuser@GenevaWoodsSBX", "gw2015!!");
        //$this->SalesOrderOBJ = new BrighttreeSalesOrderService("https://webservices.brightree.net/v0100-1501/OrderEntryService/SalesOrderService.svc", "apiuser@GenevaWoodsSBX", "gw2015!!");

		$this->patientOBJ = new BrighttreePatientService("https://webservices.brightree.net/v0100-1302/OrderEntryService/PatientService.svc", API_USERNAME, API_PASSWORD);
        $this->SalesOrderOBJ = new BrighttreeSalesOrderService("https://webservices.brightree.net/v0100-1501/OrderEntryService/SalesOrderService.svc", API_USERNAME, API_PASSWORD);		
    }

    // --------------------------------------------------------------------

    /**
     * Initialize Preferences
     *
     * @access	public
     * @param	array	initialization parameters
     * @return	void
     */
    public function patients_address_verify($params = array()) {
        
        //$result = $this->patientOBJ->PatientSearch('2015-05-19T05:25:16', '2015-05-19T05:25:17');
        $result = $this->patientOBJ->PatientSearch($params['start_date'], $params['end_date'],'',$params['records_per_page'],$params['page']);
		$this->ISP->load->model('Dashboard_Model');
		
		//exit;
		
        $xml = simplexml_load_string((string) $result);
        $totalRecords = $xml->children('s', true)->children()->PatientSearchResponse->children()->PatientSearchResult->children('a', true)->TotalItemCount;
        $records = $xml->children('s', true)->children()->PatientSearchResponse->children()->PatientSearchResult->children('a', true)->Items->children('b', true)->PatientSearchResponse;
        
        
		$returnresponse['total_patients'] = 0;
		$returnresponse['patients_updated'] = 0;
		$returnresponse['patients_not_updated'] = 0;
		$returnresponse['total_patients_exist'] = $totalRecords;
		$returnresponse['records_per_page'] = $params['records_per_page'];
		$returnresponse['page'] = $params['page'];
        
		//traverse to all records
        if($records && (count($records) > 0)) {            
            $returnresponse['total_patients'] = count($records);
            foreach ($records as $key => $record) {
                //get brightreeID
                $BrightreeID = (string) $record->children('b', true)->BrightreeID;

                //Get object of patient from brightree 
                $patient = $this->patientOBJ->PatientFetchByBrightreeID($BrightreeID);

                $xml = simplexml_load_string((string) $patient);

                $patient = $xml->children('s', true)->children()->PatientFetchByBrightreeIDResponse->children()->PatientFetchByBrightreeIDResult
                                ->children('a', true)->Items->children('b', true)->Patient;



                //get delivery address of patient
                $AddressLine1 = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine1;
                $AddressLine2 = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine2;
                $AddressLine3 = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine3;
                $City = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->City;
                $Country = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->Country;
                $County = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->County;
                $PostalCode = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->PostalCode;
                $State = (string) $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->State;


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
				
                //$address->setZip4('');

                // Add the address object to the address verify class
                $verify->addAddress($address);

                // Perform the request and return result
                $verify->verify();

                $correctAddress = $verify->getArrayResponse();
				
				//save name of patient				
				$patient_name_obj = $patient->children('b', true)->PatientGeneralInfo->children('b', true)->Name->children('c', true);
				$returnresponse['patients'][$BrightreeID]['first_name']= (string) $patient_name_obj->First;
				$returnresponse['patients'][$BrightreeID]['last_name']= (string) $patient_name_obj->Last;
				
				//save old address in response
				$old_delivery_address_obj = $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true);
				$returnresponse['patients'][$BrightreeID]['old_addr']['AddressLine1'] = (string) $old_delivery_address_obj->AddressLine1;
				$returnresponse['patients'][$BrightreeID]['old_addr']['AddressLine2'] = (string) $old_delivery_address_obj->AddressLine2;
				$returnresponse['patients'][$BrightreeID]['old_addr']['City'] = (string) $old_delivery_address_obj->City;
				$returnresponse['patients'][$BrightreeID]['old_addr']['PostalCode'] = (string) $old_delivery_address_obj->PostalCode;
				$returnresponse['patients'][$BrightreeID]['old_addr']['State'] = (string) $old_delivery_address_obj->State;

                if ($verify->isSuccess()) {
					$returnresponse['patients'][$BrightreeID]['address_verify'] = true;                    
					
                    //Now Update the correct deliveryAddress in the patient object
                    $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine1 = (isset($correctAddress['AddressValidateResponse']['Address']['Address2'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address2']) : '';
                    $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine2 = (isset($correctAddress['AddressValidateResponse']['Address']['Address1'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address1']) : '';
                    $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->City = trim($correctAddress['AddressValidateResponse']['Address']['City']);
                    $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->PostalCode = trim($correctAddress['AddressValidateResponse']['Address']['Zip5']);
                    $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->State = trim($correctAddress['AddressValidateResponse']['Address']['State']);

					
					
					//remove nil from elements
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine1->attributes('i',true)->nil);														
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->AddressLine2->attributes('i',true)->nil);														
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->City->attributes('i',true)->nil);														
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->PostalCode->attributes('i',true)->nil);														
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->State->attributes('i',true)->nil);						
					unset($patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true)->County->attributes('i',true)->nil);						
					
					
					//save old address in response
                    $new_delivery_address_obj = $patient->children('b', true)->PatientGeneralInfo->children('b', true)->DeliveryAddress->children('c', true);
                    $returnresponse['patients'][$BrightreeID]['new_addr']['AddressLine1'] = (string) $new_delivery_address_obj->AddressLine1;
					$returnresponse['patients'][$BrightreeID]['new_addr']['AddressLine2'] = (string) $new_delivery_address_obj->AddressLine2;
					$returnresponse['patients'][$BrightreeID]['new_addr']['City'] = (string) $new_delivery_address_obj->City;
					$returnresponse['patients'][$BrightreeID]['new_addr']['PostalCode'] = (string) $new_delivery_address_obj->PostalCode;					
					$returnresponse['patients'][$BrightreeID]['new_addr']['State'] = (string) $new_delivery_address_obj->State;					
					
					
					if($County || ($State && strtolower($State) != "washington")) {
						//if county exist or state of patient is not washington
						
						if(! $County){
							//if county is nil then add state as county to search taxzones
							$County=$State;
						}
						
						//look for county in database				
						$tax_codes = $this->ISP->Dashboard_Model->get_taxcode_by_county($County);							
						if(count($tax_codes) > 0) {						
							if(count($tax_codes) > 1) {
								//multiple records then update 999 taxzone
								$updated_taxzone = '369';
							}else{
								$updated_taxzone = $tax_codes[0]['taxzone_ID'];
							}
							
							// update TaxZone
							if(isset($patient->children('b',true)->PatientGeneralInfo->children('b',true)->TaxZone->children('c',true)->ID)) {
								//update value if already set
								$patient->children('b',true)->PatientGeneralInfo->children('b',true)->TaxZone->children('c',true)->ID = ((string) $updated_taxzone);
							}else	{
								//add element if already not set
								unset($patient->children('b',true)->PatientGeneralInfo->children('b',true)->TaxZone->attributes('i',true)->nil);	
								$patient->children('b',true)->PatientGeneralInfo->children('b',true)->TaxZone->addChild('c:ID', ((string) $updated_taxzone),'http://schemas.datacontract.org/2004/07/Brightree.ExternalAPI.CanonicalObjects.Common');
							}							
							
						}else{							
							//nil county value if does not exist in the database
							$patient->children('b',true)->PatientGeneralInfo->children('b',true)->DeliveryAddress->children('c',true)->County='';
						}
					}
					
                    //unset unwanted objects from xml
                    unset($patient->BrightreeID);
                    unset($patient->ExternalID);

                    $patientObjXML = str_replace(
                            array("<b:Patient>", "</b:Patient>"), array("<Patient>", "</Patient>"), $patient->asXML()
                    );

                    // Update patient object on brighttree			
                    $resultxml = simplexml_load_string((string) $this->patientOBJ->PatientUpdate($BrightreeID, $patientObjXML));
					
					$PatientUpdateResult=$resultxml->children('s', true)->children()->PatientUpdateResponse->children()->PatientUpdateResult->children('a', true);

                    //show result
                    if ((bool) $PatientUpdateResult->Success) {
                        $returnresponse['patients'][$BrightreeID]['address_update'] = true;
						$returnresponse['patients_updated'] ++ ;
                    } else {
                        $returnresponse['patients'][$BrightreeID]['address_update'] = false;
						//$returnresponse['patients_not_updated'] ++ ;
						$returnresponse['patients'][$BrightreeID]['failure_message'] = $PatientUpdateResult->Messages;
                    }
                } else {
					
					//make county nil if address is not verified
					$patient->children('b',true)->PatientGeneralInfo->children('b',true)->DeliveryAddress->children('c',true)->County='';
					//unset unwanted objects from xml
                    unset($patient->BrightreeID);
                    unset($patient->ExternalID);

                    $patientObjXML = str_replace(
                            array("<b:Patient>", "</b:Patient>"), array("<Patient>", "</Patient>"), $patient->asXML()
                    );

                    // Update patient object on brighttree			
                    $resultxml = simplexml_load_string((string) $this->patientOBJ->PatientUpdate($BrightreeID, $patientObjXML));
									
					$returnresponse['patients'][$BrightreeID]['address_verify'] = false;
					//$returnresponse['patients_not_updated'] ++ ;
					$returnresponse['patients'][$BrightreeID]['failure_message'] = $verify->getErrorMessage();                    
                }
                $verify = NULL;
            }  
        }
		return $returnresponse;
    }
	
	
	
	/**
     * Initialize Preferences
     *
     * @access	public
     * @param	array	initialization parameters
     * @return	void
     */
    public function sales_order_address_verify($params = array()) {		
		
		$result = $this->SalesOrderOBJ->SalesOrderSearch($params['start_date'], $params['end_date'],'',$params['records_per_page'],$params['page']);

		//load modal
		$this->ISP->load->model('Dashboard_Model');
		
		$xml = simplexml_load_string((string) $result);

		$totalRecords = $xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->TotalItemCount;
		$records =$xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->Items->children('b',true)->SalesOrderSearchResponse;

		$returnresponse['total_sales_orders'] = 0;
		$returnresponse['sales_orders_updated'] = 0;
		$returnresponse['sales_orders_not_updated'] = 0;
		$returnresponse['total_sales_orders_exist'] = $totalRecords;
		$returnresponse['records_per_page'] = $params['records_per_page'];
		$returnresponse['page'] = $params['page'];
		
		
		if($records && ( count($records) > 0)) {
			
			$returnresponse['total_sales_orders'] = count($records);			
			foreach($records as $key => $record)
			{
				//echo '<pre>'.print_r($record, true).'</pre>';
				//get brightreeID
				$BrightreeID = (string) $record->children('b',true)->BrightreeID;
				
				//Get object of patient from brightree 
				$sales_order = $this->SalesOrderOBJ->SalesOrderFetchByBrightreeID($BrightreeID);
				$sales_order_string = $sales_order;
				$xml = simplexml_load_string((string) $sales_order);

				$sales_order =$xml->children('s',true)->children()->SalesOrderFetchByBrightreeIDResponse->children()->SalesOrderFetchByBrightreeIDResult
						->children('a',true)->Items->children('b',true)->SalesOrder;
						
				$Address = $sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true); 
				
				//get sales order information
				$returnresponse['sales_order_status'] = (string) $record->children('b',true)->Status;				
				$returnresponse['sales_order_CreateDate']= (string) $record->children('b',true)->CreateDate;
				
				$returnresponse['sales_orders'][$BrightreeID]['sales_order_patient'] = (string) $record->children('b',true)->Patient->children('c',true)->Value;
				
				
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
				
				
				//save old address in response
				$old_delivery_address_obj = $sales_order->children('b', true)->DeliveryInfo->children('b', true)->Address->children('c', true);
				$returnresponse['sales_orders'][$BrightreeID]['old_addr']['AddressLine1'] = (string) $old_delivery_address_obj->AddressLine1;
				$returnresponse['sales_orders'][$BrightreeID]['old_addr']['AddressLine2'] = (string) $old_delivery_address_obj->AddressLine2;
				$returnresponse['sales_orders'][$BrightreeID]['old_addr']['City'] = (string) $old_delivery_address_obj->City;
				$returnresponse['sales_orders'][$BrightreeID]['old_addr']['PostalCode'] = (string) $old_delivery_address_obj->PostalCode;				
				$returnresponse['sales_orders'][$BrightreeID]['old_addr']['State'] = (string) $old_delivery_address_obj->State;
				
				
				
				if($verify->isSuccess()) {	
					
						//Now Update the correct deliveryAddress in the sales order object												
						
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine1 = (isset($correctAddress['AddressValidateResponse']['Address']['Address2'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address2']) : '';						
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine2 = (isset($correctAddress['AddressValidateResponse']['Address']['Address1'])) ? trim($correctAddress['AddressValidateResponse']['Address']['Address1']) : '';						
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->City = trim($correctAddress['AddressValidateResponse']['Address']['City']);										
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->PostalCode = trim($correctAddress['AddressValidateResponse']['Address']['Zip5']);						
						$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->State =trim($correctAddress['AddressValidateResponse']['Address']['State']);					
					
						//remove nil from elements
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine1->attributes('i',true)->nil);														
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->AddressLine2->attributes('i',true)->nil);														
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->City->attributes('i',true)->nil);														
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->PostalCode->attributes('i',true)->nil);														
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->State->attributes('i',true)->nil);
						unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID->attributes('i',true)->nil);					
											
						//save new address in response
						$new_delivery_address_obj = $sales_order->children('b', true)->DeliveryInfo->children('b', true)->Address->children('c', true);
						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['AddressLine1'] = (string) $new_delivery_address_obj->AddressLine1;
						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['AddressLine2'] = (string) $new_delivery_address_obj->AddressLine2;
						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['City'] = (string) $new_delivery_address_obj->City;
						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['PostalCode'] = (string) $new_delivery_address_obj->PostalCode;
						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['State'] = (string) $new_delivery_address_obj->State;
					
						if($County || ($State && strtolower($State) != "washington")) {
							//if county exist or state of patient is not washington							
							if(! $County){
								//if county is nil then add state as county to search taxzones
								$County=$State;
							}
							
							//look for county in database		
							$tax_codes = $this->ISP->Dashboard_Model->get_taxcode_by_county($County);
							
							if(count($tax_codes) > 0) {
							
								if(count($tax_codes) > 1) {
									//multiple records then update 999 taxzone
									$updated_taxzone = '369';
								}else{
									//If single record then update it
									$updated_taxzone = $tax_codes[0]['taxzone_ID'];
								}
								//update taxzone
								if(isset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID)){
									//update value if already set
									$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID = ((string) $updated_taxzone);
								}else{
									//add element if not set
									unset($sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->attributes('i',true)->nil);	
									$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->addChild('c:ID',((string) $updated_taxzone),'http://schemas.datacontract.org/2004/07/Brightree.ExternalAPI.CanonicalObjects.Common');
								}								
							}else{							
								//set TaxZone 999 with id 369 if does not exist in the database
								$sales_order->children('b',true)->DeliveryInfo->children('b',true)->TaxZone->children('c',true)->ID = '369';
							}
						}

						$returnresponse['sales_orders'][$BrightreeID]['new_addr']['County'] = (string) $new_delivery_address_obj->County;
						
						$SalesOrderObjXML= str_replace(
							array("<b:SalesOrder>","</b:SalesOrder>"),
							array("<SalesOrder>", "</SalesOrder>"),
							$sales_order->asXML()
						);
						
						// Update sales order object on brighttree			
						$resultxml = simplexml_load_string((string) $this->SalesOrderOBJ->SalesOrderUpdate($BrightreeID,$SalesOrderObjXML));	
						
						//show result				
						if( (bool) $resultxml->children('s',true)->children()->SalesOrderUpdateResponse->children()->SalesOrderUpdateResult->children('a',true)->Success)
						{
							$returnresponse['sales_orders'][$BrightreeID]['address_update'] = true;
							$returnresponse['sales_orders_updated'] ++ ;
							
						}else{
							$returnresponse['sales_orders'][$BrightreeID]['address_update'] = false;
							//$returnresponse['sales_orders_updated'] ++ ;
							$returnresponse['sales_orders'][$BrightreeID]['failure_message'] = $PatientUpdateResult->Messages;
						}
						
				}else {
				
					//set county nil if address is not verified
					$sales_order->children('b',true)->DeliveryInfo->children('b',true)->Address->children('c',true)->County = '';
					$SalesOrderObjXML= str_replace(
							array("<b:SalesOrder>","</b:SalesOrder>"),
							array("<SalesOrder>", "</SalesOrder>"),
							$sales_order->asXML()
						);
						
						
						
					// Update sales order object on brighttree			
					$resultxml = simplexml_load_string((string) $this->SalesOrderOBJ->SalesOrderUpdate($BrightreeID,$SalesOrderObjXML));	
					
					$returnresponse['sales_orders'][$BrightreeID]['address_verify'] = false;					
					$returnresponse['sales_orders'][$BrightreeID]['failure_message'] = $verify->getErrorMessage();    
				}				
				$verify = NULL;		
			}				
		}
		return $returnresponse;
	}
	
	
	
}