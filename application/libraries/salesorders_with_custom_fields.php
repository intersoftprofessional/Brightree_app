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
class Salesorders_With_Custom_Fields {

    var $cfs;
	var $SalesOrderOBJ;
	var $default_Order_Labels_Required = Default_Value_Of_Customfield_Order_Labels_Required;

    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */
    public function __construct($params = array()) {
		require_once("class/class.brighttreesalesorderservice.php");
        require_once("class/class.customfieldservice.php");        
        
		//get instance
		$this->ISP =& get_instance();
		
		//create objects of CustomFieldService and BrighttreeSalesOrderService
		$this->cfs = new CustomFieldService("https://webservices.brightree.net/v0100/CustomFieldService/CustomFieldService.svc",API_USERNAME,API_PASSWORD);
        $this->SalesOrderOBJ = new BrighttreeSalesOrderService("https://webservices.brightree.net/v0100-1501/OrderEntryService/SalesOrderService.svc", API_USERNAME, API_PASSWORD);
    }
	
    /*Function to get Ready To Shipment Sales Order's data*/
	 public function fetch_sales_order_ready_for_shipping($params = array()) {
		$result = $this->SalesOrderOBJ->SalesOrderSearch($params['start_date'], $params['end_date'],'',$params['records_per_page'],$params['page'],$params['WIPUserTaskReason']);
		
		//load modal
		$this->ISP->load->model('Dashboard_Model');
		
		$xml = simplexml_load_string((string) $result);

		$totalRecords = $xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->TotalItemCount;
		$records =$xml->children('s',true)->children()->SalesOrderSearchResponse->children()->SalesOrderSearchResult->children('a',true)->Items->children('b',true)->SalesOrderSearchResponse;

		$returnresponse['total_sales_orders_exist'] = $totalRecords;		
		$count=1;
		$return_array= array();
		
		if($records && ( count($records) > 0)) {			
						
			foreach($records as $key => $record)
			{		
				$BrightreeID = (string) $record->children('b',true)->BrightreeID;				
				
				//Get object of patient from brightree 
				$sales_order = $this->SalesOrderOBJ->SalesOrderFetchByBrightreeID($BrightreeID);
				$sales_order_string = $sales_order;
				$xml = simplexml_load_string((string) $sales_order);

				$sales_order =$xml->children('s',true)->children()->SalesOrderFetchByBrightreeIDResponse->children()->SalesOrderFetchByBrightreeIDResult
						->children('a',true)->Items->children('b',true)->SalesOrder;
				$WIPInfo =$sales_order->children('b',true)->SalesOrderWIPInfo;		
						
				$return_array[$count]['WIPAssignedToKey'] = (string) $WIPInfo->children('b',true)->WIPAssignedToKey; 
				$return_array[$count]['WIPAssignedToPerson'] = (string) $WIPInfo->children('b',true)->WIPAssignedToPerson; 
				$WIPClosedDate = (string) $WIPInfo->children('b',true)->WIPClosedDate; 
				$WIPCompleted = (bool) $WIPInfo->children('b',true)->WIPCompleted; 
				$WIPCreateDate = (string) $WIPInfo->children('b',true)->WIPCreateDate; 
				$return_array[$count]['WIPDaysInState'] = (string) $WIPInfo->children('b',true)->WIPDaysInState; 
				$WIPNeedDate = (string) $WIPInfo->children('b',true)->WIPNeedDate; 
				$return_array[$count]['WIPStateKey'] = (string) $WIPInfo->children('b',true)->WIPStateKey; 
				$return_array[$count]['WIPStateName'] = (string) $WIPInfo->children('b',true)->WIPStateName;
				
				if($WIPInfo->children('b',true)->WIPNeedDate) {
					$date = new DateTime((string) $WIPInfo->children('b',true)->WIPNeedDate);
					$return_array[$count]['WIPNeedDate'] = $date->format('Y-m-d');
				}else {
					$return_array[$count]['WIPNeedDate'] = '';
				}	
				
				if($WIPInfo->children('b',true)->WIPCreateDate) {
					$date = new DateTime((string) $WIPInfo->children('b',true)->WIPCreateDate);
					$return_array[$count]['WIPCreateDate'] = $date->format('Y-m-d');
				}else {
					$return_array[$count]['WIPCreateDate'] = '';
				}
				
				if($WIPInfo->children('b',true)->WIPClosedDate) {
					$date = new DateTime((string) $WIPInfo->children('b',true)->WIPClosedDate);
					$return_array[$count]['WIPClosedDate'] = $date->format('Y-m-d');
				}else {
					$return_array[$count]['WIPClosedDate'] = '';
				}

				$WIPCompleted= (string) $WIPInfo->children('b',true)->WIPCompleted;				
				$return_array[$count]['WIPCompleted'] = ($WIPCompleted == 'false') ? '0':'1';
				
				$return_array[$count]['sales_order_id'] = $BrightreeID;
				$return_array[$count]['facility_id']= (string) $sales_order->children('b',true)->DeliveryInfo->children('b',true)->Facility->children('c',true)->ID;
				$return_array[$count]['facility']= (string) $sales_order->children('b',true)->DeliveryInfo->children('b',true)->Facility->children('c',true)->Value;
				
				
				$return_array[$count]['patient_id']= (string) $sales_order->children('b',true)->SalesOrderClinicalInfo->children('b',true)->Patient->children('b',true)->BrightreeID;
				$patient_name_first= (string) $sales_order->children('b',true)->SalesOrderClinicalInfo->children('b',true)->Patient->children('b',true)->Name->children('c',true)->First;
				$patient_name_last= (string) $sales_order->children('b',true)->SalesOrderClinicalInfo->children('b',true)->Patient->children('b',true)->Name->children('c',true)->Last;
				$patient_name_middle= (string) $sales_order->children('b',true)->SalesOrderClinicalInfo->children('b',true)->Patient->children('b',true)->Name->children('c',true)->Middle;
				
				$patient_name=($patient_name_first) ? trim($patient_name_first) : '';
				$patient_name .=($patient_name_middle) ? ' '.trim($patient_name_middle) : '';
				$patient_name .=($patient_name_last) ? ' '.trim($patient_name_last) : '';
				
				$return_array[$count]['patient_name'] = trim($patient_name); 
				
				$return_array[$count]['labels_required'] = $this->fetch_CustomFieldValue(array(			
					'category' => 'SalesOrder',
					'brightreeID' => $BrightreeID,
					'FieldStorageNumber'=>FieldStorageNumber_For_Customfield_Order_Labels_Required
				)); 	
				
				$count++;
				
			}
		}
		return $return_array;
	}
	
	 /*Function to get Ready To Shipment Sales Order's data*/
	 public function fetch_CustomFieldValue($params = array()) {
		$result = $this->cfs->CustomFieldValueFetchAllByBrightreeID($params['category'],$params['brightreeID']);		
		$xml = simplexml_load_string((string) $result);
		$customfields =$xml->children('s',true)->children()->CustomFieldValueFetchAllByBrightreeIDResponse->children()->CustomFieldValueFetchAllByBrightreeIDResult->children('a',true)->Items->children('b',true)->CustomFieldValue;
		$value=$this->default_Order_Labels_Required;
		if(count($customfields) > 0) {
		
			foreach($customfields as $customfield) {
				if( (string) $customfield->children('b',true)->FieldStorageNumber == $params['FieldStorageNumber']){
					$value= (string) $customfield->children('b',true)->Value;
					break;
				}
			}
		}
		return $value;
	 }
}