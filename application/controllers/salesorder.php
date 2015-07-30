<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SalesOrder extends Isp_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    var $module_name = 'SalesOrder';
    var $verify_sales_order_address = 'salesorder/verify_sales_order_address';
	var $list_affected_sales_orders_by_api = 'salesorder/list_affected_sales_orders_by_api';
    //var $order_view = 'offers/order_view';
    var $model_name = 'SalesOrder_Model';

    //var $edit_view = 'offers/edit_view';

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }

    public function verify_sales_order_address($id = '', $msg = '', $redirect = 'true') {
        if ($this->session->userdata('user_level') == '1') {		
			
			if($msg == 'deleted')
			$this->data['msg'] = 'Sales order delivery address update request deleted successfully';
			
			//get all info from api_patient_update_call_lists table
			$table_name = 'api_sales_order_update_call_lists';            
            $this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');		
			

            if ((!isset($_POST['CreateDateTimeStart'])) && (!isset($_POST['CreateDateTimeEnd'])))
                $this->load->view($this->verify_sales_order_address, $this->data);
            else if (empty($_POST['CreateDateTimeStart']) || empty($_POST['CreateDateTimeEnd'])) {
                $this->data['msg'] = "Both Dates are required";
                $this->load->view($this->verify_sales_order_address, $this->data);
            } else {
               echo  $this->load->library('Address_Verify');
               
                $result = $this->address_verify->sales_order_address_verify(array(
                    'start_date' => $_POST['CreateDateTimeStart'],
                    'end_date' => $_POST['CreateDateTimeEnd']
                ));
				
				//save sales order api call lists
				$sales_order_api_call_list_id = $this->save_sales_order_api_call_lists($result,$_POST['CreateDateTimeStart'],$_POST['CreateDateTimeEnd']);
				
				//save affected sales order api call lists
				if($sales_order_api_call_list_id) {
					if($result['total_sales_orders']) {
						$this->save_affected_sales_order_lists($sales_order_api_call_list_id, $result['sales_orders']);
						$this->data['msg'] = "Total Sales Order Found: ".$result['total_sales_orders'].", Total sales order's delivery address updated : ".$result['sales_orders_updated']."</br><a target='_blank' href='".site_url('salesorder/list_affected_sales_orders_by_api/'.$sales_order_api_call_list_id)."'>Click To view Results</a>";
					}
					else {
						$this->data['msg'] = "No Sales Order Found";
					}
					$this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');					
					$this->load->view($this->verify_sales_order_address, $this->data);
				}								
            }
        } else
            echo "You have no permission to access this page.";
    }

	function save_sales_order_api_call_lists($data = array(),$startdate = '',$enddate='')  {
		$table_name = 'api_sales_order_update_call_lists';
		
		$form_data['total_sales_orders']=$data['total_sales_orders'];
		$form_data['sales_orders_updated']=$data['sales_orders_updated'];
		$form_data['sales_orders_not_updated']=$data['sales_orders_not_updated'];		
		$form_data['sales_order_create_start_date']=$startdate;
		$form_data['sales_order_create_end_date']=$enddate;		
		$form_data['time']= date('Y-m-d H:i:s');
		return $this->{$this->model_name}->__insert_table($form_data,$table_name);	
	}
	
	
	function save_affected_sales_order_lists($sales_order_api_call_list_id,$sales_orders=array())  {
		$table_name = 'api_affected_sales_order_lists';
		
		$form_data['sales_order_api_call_id']= $sales_order_api_call_list_id;
		if($sales_orders && (count($sales_orders) > 0)) {			
			foreach($sales_orders as $sales_orderID => $sales_order) {
				$form_data['sales_order_id']=$sales_orderID;				
				$form_data['address_updated']= (isset($sales_order['address_update']) && $sales_order['address_update']) ? '1' : '0';
				$form_data['failure_message']= (isset($sales_order['failure_message'])) ? $sales_order['failure_message'] : '';
				$form_data['old_address']= ((! empty($sales_order['old_addr']['AddressLine1'])) ? trim($sales_order['old_addr']['AddressLine1']).', ' : '').
										   ((! empty($sales_order['old_addr']['AddressLine2'])) ? trim($sales_order['old_addr']['AddressLine2']).', ' : '').
										   ((! empty($sales_order['old_addr']['City'])) ? trim($sales_order['old_addr']['City']).', ' : '').
										   ((! empty($sales_order['old_addr']['PostalCode'])) ? trim($sales_order['old_addr']['PostalCode']).', ' : '').
										   //((! empty($sales_order['old_addr']['County'])) ? trim($sales_order['old_addr']['County']).', ' : '').
										   ((! empty($sales_order['old_addr']['State'])) ? trim($sales_order['old_addr']['State']) : '');
										   
				$form_data['new_address']= ((! empty($sales_order['new_addr']['AddressLine1'])) ? trim($sales_order['new_addr']['AddressLine1']).', ' : '').
										   ((! empty($sales_order['new_addr']['AddressLine2'])) ? trim($sales_order['new_addr']['AddressLine2']).', ' : '').
										   ((! empty($sales_order['new_addr']['City'])) ? trim($sales_order['new_addr']['City']).', ' : '').
										   ((! empty($sales_order['new_addr']['PostalCode'])) ? trim($sales_order['new_addr']['PostalCode']).', ' : '').
										  // ((! empty($sales_order['new_addr']['County'])) ? trim($sales_order['new_addr']['County']).', ' : '').
										   ((! empty($sales_order['new_addr']['State'])) ? trim($sales_order['new_addr']['State']) : '');	

				$form_data['patient_name']=trim($sales_order['sales_order_patient']);								
				$this->{$this->model_name}->__insert_table($form_data,$table_name);
			}
		}
	}
	
	function list_affected_sales_orders_by_api($api_call_id=0,$msg='')
	{
		if ($this->session->userdata('user_level') == '1') {
		 
			if($msg == 'deleted')
			$this->data['msg'] = 'Affected Sales order information deleted successfully';
			
			//get all retailers info from sos_retailers table
			$table_name = 'api_affected_sales_order_lists';
			$form_data['sales_order_api_call_id'] = $api_call_id;
            $get_all_affected_sales_orders_lists = $this->{$this->model_name}->__select_table($form_data,$table_name);
            
			
			$all_affected_sales_orders = $get_all_affected_sales_orders_lists->result_array();
            $this->data['beans'] = $all_affected_sales_orders;					
			$this->load->view($this->list_affected_sales_orders_by_api, $this->data);
		}
		
	}
	
	function delete_sales_order_api_request($sales_order_api_call_id=0)  {
		if ($this->session->userdata('user_level') == '1') {			
			
			//delete affected sales orders first
			$table_name = 'api_affected_sales_order_lists';
			$form_data['sales_order_api_call_id'] = $sales_order_api_call_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name);
			
			//delete sales order update requests
			$table_name = 'api_sales_order_update_call_lists';
			$form_data['ID'] = $sales_order_api_call_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name); 
			
			$msg = 'deleted';								
			$this->verify_sales_order_address('',$msg);			
		}else
            echo "You have no permission to access this page.";	
	}
	
	function delete_affected_sales_order($affected_sales_order_id=0, $sales_order_api_call_id=0)  {
		
		if ($this->session->userdata('user_level') == '1') {		 
			
			//get all retailers info from sos_retailers table
			$table_name = 'api_affected_sales_order_lists';
			$form_data['ID'] = $affected_sales_order_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name);    
			
			$msg = 'deleted';								
			$this->list_affected_sales_orders_by_api($api_call_id,$msg);			
		}else
            echo "You have no permission to access this page.";	
	}
	
	
	function get_data_from_table($table_name, $orderby, $order){
		$get_all_api_call_lists = $this->{$this->model_name}->__select_all_datas_table($table_name, $orderby, array(), $order);            
			
		return $get_all_api_call_lists->result_array();	
	}
	
    function order_view() {
        $data = $this->list_view('', '', 'false');
        $this->load->view($this->order_view, $data);
    }

    //here save values in the database.
    public function save() {
        //print_r($_POST); exit;
        $save_data = $this->input->post();
        foreach ($save_data as $id => $value) {
            if ($id != 'offers1' && $id != 'set_homepage1' && $id != 'winning_number' && $id != 'order1')
                $form_data[$id] = $value;
        }
        $files = $_FILES;

        if (isset($save_data['list_id']) && $save_data['list_id'] != '') {
            $where['list_id'] = $save_data['list_id'];
            $this->{$this->model_name}->__update_table($where, $form_data, 'offer_lists');
            $this->save_offer_list($where['list_id'], $_POST, 'edit');
            $id = $save_data['list_id'];
            $msg = 'edited';
        } else {
            $id = $this->{$this->model_name}->__insert($form_data);
            $this->save_offer_list($id, $_POST, 'edit');
            $msg = 'added';
        }
        redirect(site_url('offers/list_view/' . $id . '/' . $msg));
    }

    function save_offer_list($list_id = '', $data = array(), $page = '') {
        //print_r($data); exit;
        if ($page == 'edit') {
            $i = 1;
            $list_id = $list_id;
            $save_data = $data;
        } else {
            $i = '';
            $save_data = $this->input->post();
            $list_id = $save_data['list_id'];
        }
        if (isset($save_data['offers' . $i])) {
            $off = 1;
            foreach ($save_data['offers' . $i] as $id => $value) {
                $form_data = array();
                $form_data['list_id'] = $list_id;
                $form_data['retailer_id'] = $id;
                $check = $this->{$this->model_name}->__select_table($form_data, 'offers');
                $rows = $check->result_array();
                if (!empty($rows)) {
                    if ($value == '')
                        $form_data['order'] = '0';
                    else if ($save_data['order' . $i][$id] == '0') {
                        $order = $this->{$this->model_name}->get_max_order_val($list_id);
                        $form_data['order'] = $order + 1;
                    } else
                        $form_data['order'] = $save_data['order' . $i][$id];
                    $form_data['offer'] = htmlentities($value);
                    if (isset($save_data['set_homepage' . $i][$id]))
                        $form_data['set_homepage'] = $save_data['set_homepage' . $i][$id];
                    else
                        $form_data['set_homepage'] = '0';
                    $where['list_id'] = $save_data['list_id'];
                    $where['retailer_id'] = $id;
                    $this->{$this->model_name}->__update_table($where, $form_data, 'offers');

                    $form_list['winning_number'] = $save_data['winning_number'];
                    $where_list['list_id'] = $save_data['list_id'];
                    $this->{$this->model_name}->__update_table($where_list, $form_list, 'offer_lists');
                }
                else {
                    $form_list['winning_number'] = $save_data['winning_number'];
                    $where_list['list_id'] = $save_data['list_id'];
                    $this->{$this->model_name}->__update_table($where_list, $form_list, 'offer_lists');

                    $form_data['offer'] = htmlentities($value);
                    if (isset($save_data['set_homepage' . $i][$id]))
                        $form_data['set_homepage'] = $save_data['set_homepage' . $i][$id];
                    else
                        $form_data['set_homepage'] = '0';
                    if ($value == '')
                        $form_data['order'] = '0';
                    else {
                        $form_data['order'] = $off;
                        $off++;
                    }
                    $id = $this->{$this->model_name}->__insert_table($form_data, 'offers');
                }
            }
        }
        if ($page == '')
            redirect(site_url('offers/list_view'));
    }

    function change_live_list() {
        $list_id = $_POST['live_list_id'];
        $get_all_offer_lists = $this->{$this->model_name}->__select_all_data('list_name');
        $all_offer_lists = $get_all_offer_lists->result_array();
        if (!empty($all_offer_lists)) {
            foreach ($all_offer_lists as $list) {
                if ($list['list_id'] == $list_id)
                    $status = '1';
                else
                    $status = '0';
                $where['list_id'] = $list['list_id'];
                $save_data['status'] = $status;
                $this->{$this->model_name}->__update_table($where, $save_data, 'offer_lists');
                $msg = 'set_live';
            }
        }
        redirect(site_url('offers/list_view/' . $msg));
    }

    function check_list_name() {
        $id = $_POST['list_id'];
        $list_name = $_POST['list_name'];
        $form_data['list_name'] = $list_name;
        if ($id != '')
            $where = $id;
        else
            $where = '';
        $check = $this->{$this->model_name}->__select_table($form_data, 'offer_lists', '', $where);
        $row = $check->result_array();
        if (empty($row))
            echo "true";
    }

    function update_order() {
        $array = $_POST['arrayorder'];
        $list_id = $_POST['list_id'];
        //print_r($array); exit;
        if ($_POST['update'] == "update") {
            $this->{$this->model_name}->__update_order($array, $list_id);
        }
        echo "<p><strong>SUCCESS: </strong><span>The offer order have been updated.</span></p>";
    }

    function generate_email() {
        $form_ret['user_level'] = '0';
        $get_all_retailer_list = $this->{$this->model_name}->__select_table($form_ret, 'sos_retailers', 'business_name');
        $all_retailer = $get_all_retailer_list->result_array();

        $form_data['status'] = '1';
        $form_data['list_id'] = $_POST['list_id'];
        $get_live_list_offers = $this->{$this->model_name}->__select_table($form_data, 'offers', 'order');
        $all_offers = $get_live_list_offers->result_array();
        if (!empty($all_offers)) {
            $offers = array();
            foreach ($all_offers as $offer) {
                $offers[$offer['list_id']][$offer['retailer_id']] = $offer;
            }
        }
        if (isset($all_retailer)) {
            $offer = array();
            foreach ($all_retailer as $retailer) {
                if ($offers[$_POST['list_id']][$retailer['retailer_id']]['offer'] != '') {
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['id'] = $retailer['retailer_id'];
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['number'] = $retailer['retailer_member_number'];
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['slug'] = $retailer['slug_business_name'];
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['image'] = $retailer['main_profile_image'];
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['name'] = $retailer['business_name'];
                    $offer[$offers[$_POST['list_id']][$retailer['retailer_id']]['order']]['offer'] = $offers[$_POST['list_id']][$retailer['retailer_id']]['offer'];
                }
            }
        }
        ksort($offer);
        $save_pdth = 'http://' . $_SERVER['HTTP_HOST'] . '/test/img/offer_thumb/87x65';
        $base_url = 'http://' . $_SERVER['HTTP_HOST'];
        //print_r($offer); exit;
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sold on Stourport - November offers</title>
</head>
<style type="text/css">
	@media screen and (-webkit-min-device-pixel-ratio:0) {
body { width:100% !important;  }
}
</style>
<body style="margin:0;padding:0;width:600px; -moz-text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%">


<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="blue-box" style="margin:0 auto !important; max-width:600px !important ; width:100%;">
    <tr>
        <td width="100%">
           
            <table cellpadding="0" cellspacing="0" border="0" width="100%" >
       
            	<tr>
                	<td align="left" valign="top" width="50%" bgcolor="#1e3ea4" height="206">
                    <table width="80%" bgcolor="#1e3ea4" border="0" cellspacing="0" cellpadding="0"height="206" >
                <tr>
                    <td align="left" valign="top" style="padding-top:20px; padding-left:10px;"><span style="color:#fff; font-size:20px; line-height:20px; font-family:Arial, Helvetica, sans-serif; ">25 Fantastic Offers for November</span></td>
                </tr>
                <tr>
                	<td align="left" valign="top"  style="color:#ffffff;font-family:Arial, sans-serif;font-size:14px;line-height:22px; padding-top:12px; padding-left:10px;">
                        As well as lots of great new offers we also have new retailers joining the scheme. If you\'re thinking of starting your Chrismas shopping early then why not grab your Sold on Stourport card and <span style="font-weight:bold">stay local and save</span> this November?</td>
                </tr>
                <tr>
                	
                    	 <td align="left" valign="bottom" width="346">
                    		<img  src="' . $base_url . '/images/offer/blue-left-bot.jpg" width="346" height="16" alt="" style="display:block; float:left; border:1px solid red vertical-align:top;" />
                    </td>
                    
                </tr>
            </table>
         
                    </td>
                    <td align="left" valign="top" width="254">
                    		<img  src="' . $base_url . '/images/offer/blue-right.jpg" width="254" height="206" alt="" style="display:block; float:left; border:1px solid red vertical-align:top;" />
                    </td>
                </tr>
                 
            </table>
        </td>
    </tr>
   
    <tr>
        <td width="100%" height="30">&nbsp;</td>
    </tr>
</table>




<!-- OFFER 1 START -->';
        if (!empty($offer)) {
            $i = 1;
            foreach ($offer as $offer1) {
                if ($i <= 4) {
                    $html .= '<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto; max-width:600px; width:100%;">
    <tr>
            <td align="left" valign="middle" width="15%">
            	<img src="';
                    if ($offer1['image'] != '')
                        $html .= $save_pdth . '/' . $offer1['image'];
                    else
                        $html .= $save_pdth . '/default.jpg';
                    $html .= '" alt="Stourport-Photo-Centre"  style="  -moz-box-sizing: border-box; border: 1px solid #CCCCCC;  border-radius: 5px; max-width:87px;" width="87" height="65" />
            </td>
           <td align="left" valign="top" width="5%">&nbsp;</td>
            <td align="left" valign="top" width="55%" >
            	<h2 style="font-family:Arial, Helvetica, sans-serif ; font-size:18px; line-height:20px; color:#E64578;">' . $offer1['name'] . '</h2>
                <p style="font-size:16px; color:#1D3876; line-height:18px; font-family:Arial, Helvetica, sans-serif;">' . $offer1['offer'] . '</p>
            </td>
               <td align="left" valign="top" width="5%">&nbsp;</td>
              <td align="left" valign="middle"><a href="' . $base_url . '/retailer/' . $offer1['number'] . '/' . $offer1['slug'] . '.html">
              	<img src="http://soldonstourport.co.uk/img/email_thumbs/view-page.png" alt="Stourport-Photo-Centre"  style="border:none;max-width:56px;" width="53" height="35" />
              </a></td>
    </tr>
    <tr>
        <td width="100%" height="41" colspan="5"><img src="http://www.soldonstourport.co.uk/images/separator.png" width="600" height="41" alt="" style="display:block; width:100%; max-width:600px;" /></td>
    </tr>
</table>';
                }
                if ($i == 4) {
                    $html .= '<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="pink-box" style="margin:0 auto; max-width:600px; width:100%;">
    <tr>
        <td width="29%" class="nodisplay" valign="top"><img  src="' . $base_url . '/images/offer/pink-left.jpg" width="233" height="198" alt="" style="display:block; vertical-align:top;" /></td>
        <td width="70%" valign="top" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            	<td width="100%" class="nodisplay" valign="top" align="left"><img  src="' . $base_url . '/images/offer/pink-rght-top.jpg" width="367" height="17" alt="" style="display:block; vertical-align:top;" /></td>
            </tr>
                <tr>
                    <td width="100%" align="left" bgcolor="#e881a6" style="color:#ffffff;font-family:Arial, sans-serif;font-size:22px;font-weight:bold"><img  src="http://www.soldonstourport.co.uk/images/mp.png" width="333" height="29" alt="£100&nbsp;Monthly&nbsp;Prize&nbsp;Draw!" style="display:block" /></td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  bgcolor="#e881a6">
                <tr>
                	<td colspan="2" height="66" bgcolor="#e881a6">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0" >
	
        	<tr>
                    
                    <td width="100%" align="right" style="color:#ffffff;font-family:Arial, sans-serif;font-size:14px;line-height:18px">Win £100 in vouchers to spend at participating retailers.</td>
                </tr>
                <tr>
                    <td width="100%" colspan="2" align="right" style="color:#ffffff;font-family:Arial, sans-serif;font-size:14px;line-height:18px">If you\'re a winner please contact Severn Stitches, Lombard St.</td>
                </tr>
                <tr>
                    <td width="100%" height="10" colspan="2" style="font-size:1px;line-height:1px">&nbsp;</td>
                </tr>
     
</table>
                    </td>
                </tr>
                <tr>
                    <td width="100%" height="24" colspan="2" align="right" style="color:#ffffff;font-family:Arial, sans-serif;font-size:16px;font-weight:bold"><img src="' . $base_url . '/images/offer/wcn-november-2013-.png" width="367" height="24" alt="November\'s winning card number: 1219" style="display:block" /></td>
                </tr>
                    <tr>
                    <td width="100%" class="nodisplay"  valign="top" align="left"><img  src="' . $base_url . '/images/offer/pink-rght-bot.jpg" width="367" height="62" alt="" style="display:block; vertical-align:top;" /></td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>';
                }
                if ($i > 4) {
                    $html .= '<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto; max-width:600px; width:100%;">
    <tr>
            <td align="left" valign="middle" width="15%">
            	<img src="';
                    if ($offer1['image'] != '')
                        $html .= $save_pdth . '/' . $offer1['image'];
                    else
                        $html .= $save_pdth . '/default.jpg';
                    $html .= '" alt="Stourport-Photo-Centre"  style="  -moz-box-sizing: border-box; border: 1px solid #CCCCCC;  border-radius: 5px; max-width:87px;" width="87" height="65" />
            </td>
           <td align="left" valign="top" width="5%">&nbsp;</td>
            <td align="left" valign="top" width="55%" >
            	<h2 style="font-family:Arial, Helvetica, sans-serif ; font-size:18px; line-height:20px; color:#E64578;">' . $offer1['name'] . '</h2>
                <p style="font-size:16px; color:#1D3876; line-height:18px; font-family:Arial, Helvetica, sans-serif;">' . $offer1['offer'] . '</p>
            </td>
               <td align="left" valign="top" width="5%">&nbsp;</td>
              <td align="left" valign="middle"><a href="' . $base_url . '/retailer/' . $offer1['number'] . '/' . $offer1['slug'] . '.html">
              	<img src="http://soldonstourport.co.uk/img/email_thumbs/view-page.png" alt="Stourport-Photo-Centre"  style="border:none;max-width:56px;" width="53" height="35" />
              </a></td>
    </tr>
    <tr>
        <td width="100%" height="41" colspan="5"><img src="http://www.soldonstourport.co.uk/images/separator.png" width="600" height="41" alt="" style="display:block; width:100%; max-width:600px;" /></td>
    </tr>
</table>';
                }
                $i++;
            }
        }
        $html .= '<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto; max-width:600px; width:100%;">
    <tr>
        <td width="100%" height="45" align="center" valign="top" style="color:#d23c68;font-family:Arial, sans-serif;font-size:22px;font-weight:bold"><img src="http://www.soldonstourport.co.uk/images/rules.png" width="323" height="25" alt="Rules&nbsp;of&nbsp;the&nbsp;Scheme" style="display:block; width:323px;" /></td>
    </tr>
    <tr>
        <td width="100%">
            <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
                <tr>
                    <td width="10%" valign="top" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">1.</td>
                    <td width="90%" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">
                        Only one membership card may be held by any individual. In the event of more<br style="line-height:18px" />
                        than one card being held, eligibility to the prize draw is forfeited for all those cards.
                    </td>
                </tr>
                <tr>
                    <td width="100%" height="5" colspan="2" style="font-size:1px;line-height:1px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="10%" valign="top" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">2.</td>
                    <td width="90%" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">
                        In the event of loss of the membership card, a replacement card will be issued<br style="line-height:18px" />
                        and the lost card will be cancelled.
                    </td>
                </tr>
                <tr>
                    <td width="100%" height="5" colspan="2" style="font-size:1px;line-height:1px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="10%" valign="top" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">3.</td>
                    <td width="90%" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">
                        Winners of the prize draw may spend their £100 at up to five different Supporting<br style="line-height:18px" />
                        Retailers. Winners must make their claim within the month that their number is<br style="line-height:18px" />
                        drawn, and must spend their winnings withi n three months of their claim.
                    </td>
                </tr>
                <tr>
                    <td width="100%" height="5" colspan="2" style="font-size:1px;line-height:1px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="10%" valign="top" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">4.</td>
                    <td width="90%" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">
                        In the event that a member wishes to come out of the scheme, they will write to<br style="line-height:18px" />
                        The Administrators, Stourport Town Centre Forum at PO Box 2725, Stourport-on-<br style="line-height:18px" />
                        Severn, DY13 8QN and their details will be removed.
                    </td>
                </tr>
                <tr>
                    <td width="100%" height="5" colspan="2" style="font-size:1px;line-height:1px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="10%" valign="top" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">5.</td>
                    <td width="90%" style="color:#21488f;font-family:Arial, sans-serif;font-size:14px;line-height:18px">Should there be any dispute, the decision of The Administrators will be final.</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="100%"  align="center" valign="bottom"><img src="http://www.soldonstourport.co.uk/images/sos.jpg" width="466" height="360" alt="" style="display:block; max-width:466px; width:100%" /></td>
    </tr>
    <tr>
        <td width="100%"  align="center" valign="top" style="font-family:Arial, sans-serif;font-size:14px;font-weight:bold"><a href="http://www.crayonjuice.co.uk" target="_blank" style="color:#f79422;text-decoration:none"><img src="http://www.soldonstourport.co.uk/images/cjlogo.png" width="136" height="27" alt="crayonjuice" style="display:block;border:none; max-width:136px;" /></a></td>
    </tr>
    <tr>
        <td width="100%" align="center" style="color:#979797;font-family:Arial, sans-serif;font-size:11px;line-height:14px; text-align:center !important">
            Crayon Juice is providing all design, illustration, branding, printing<br style="line-height:14px" />
            and web development services for Sold on Stourport.<br style="line-height:14px" />
            24 Lombard Street, Stourport-on-Severn, DY13 8DT - <a href="http://www.crayonjuice.co.uk" target="_blank" style="color:#979797;text-decoration:none;line-height:14px">www.crayonjuice.co.uk</a>
        </td>
    </tr>
    <tr>
        <td width="100%" height="20">&nbsp;</td>
    </tr>
</table>

</body>
</html>
';
        echo htmlentities($html);
    }

}