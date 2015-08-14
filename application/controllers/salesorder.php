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
	var $salesorder_per_page = TOTAL_ADDRESSES_PROCESSED_PER_REQUEST;	

    //var $edit_view = 'offers/edit_view';

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }

    public function verify_sales_order_address($page = 1, $msg = '', $redirect = 'true') {

        if ($this->session->userdata('user_level') == '1') {		
			
			if($msg == 'deleted')
			$this->data['msg'] = 'Sales order delivery address update request deleted successfully';
			
			//get all info from api_patient_update_call_lists table
			$table_name = 'api_sales_order_update_call_lists';            
            $this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');		
			
			//unset session if page 1 is called again without processing all steps
			if((! $page) || empty($page) || $page == 1) {
				$this->session->unset_userdata('SOCreateDateTimeStart');
				$this->session->unset_userdata('SOCreateDateTimeEnd');
				$this->session->unset_userdata('total_sales_order_processed');
			}

			//show view if no data is set
			if ((!isset($_POST['CreateDateTimeStart'])) && (!$this->session->userdata('SOCreateDateTimeStart'))) {
				$this->load->view($this->verify_sales_order_address, $this->data);
				return;
			}


			
			//set values
			$data['CreateDateTimeStart'] = (isset($_POST['CreateDateTimeStart'])) ? $_POST['CreateDateTimeStart'] : $this->session->userdata('SOCreateDateTimeStart');
			$data['CreateDateTimeEnd'] = (isset($_POST['CreateDateTimeEnd'])) ? $_POST['CreateDateTimeEnd'] : $this->session->userdata('SOCreateDateTimeEnd');       
                
            if (empty($data['CreateDateTimeStart']) || empty($data['CreateDateTimeEnd'])) {
                $this->data['msg'] = "Both Dates are required";
                $this->load->view($this->verify_sales_order_address, $this->data);
            } else {
				
               	$this->proceed_to_verify_address($data,$page);							
            }
        } else
            echo "You have no permission to access this page.";
    }
	
	
	function proceed_to_verify_address($data,$page){
		$table_name = 'api_sales_order_update_call_lists'; 
		$total_sales_order_processed=0;
		//load library
		$this->load->library('Address_Verify');
               
		$result = $this->address_verify->sales_order_address_verify(array(
			'start_date' => $data['CreateDateTimeStart'],
			'end_date' => $data['CreateDateTimeEnd'],
			'records_per_page' => $this->salesorder_per_page,
			'page' => $page
		));
		
		//save sales order api call lists
		$sales_order_api_call_list_id = $this->save_sales_order_api_call_lists($result,$data['CreateDateTimeStart'],$data['CreateDateTimeEnd']);
		
		//save affected sales order api call lists
		if($sales_order_api_call_list_id) {
			if($result['total_sales_orders']) {
				//update affected sales orders
				$this->save_affected_sales_order_lists($sales_order_api_call_list_id, $result['sales_orders']);
				
				//check if more then maximum record limit records exist in the request
				if($result['total_sales_orders'] < $result['total_sales_orders_exist']) {
					
					$record_processed_from= (intval($result['page']) * $this->salesorder_per_page) -  $this->salesorder_per_page + 1; 
					$record_processed_to= $record_processed_from + intval($result['total_sales_orders']) - 1;
					
					$this->data['msg'] = "Total sales order exist in your request: "
								.$result['total_sales_orders_exist']
								."<br> Records Processed: "
								.$record_processed_from.' TO '.$record_processed_to
								.". Total Sales Order Found: "
								.$result['total_sales_orders']
								.", Total sales order's delivery address updated : "
								.$result['sales_orders_updated'];								
					
					$total_sales_order_processed = ($this->session->userdata('total_sales_order_processed') ? intval($this->session->userdata('total_sales_order_processed')) + $result['total_sales_orders'] : $result['total_sales_orders']);
					$remaning_records = intval($result['total_sales_orders_exist']) - $total_sales_order_processed;
					$next_records = ($remaning_records < $this->salesorder_per_page) ? $remaning_records : $this->salesorder_per_page;
					
					$this->data['msg'].="</br></br>Total ".$total_sales_order_processed." Sales Orders have been processed out of ".$result['total_sales_orders_exist'];
					
					//check if it is a last page
					if($result['page'] != ceil((floatval($result['total_sales_orders_exist']) / $this->salesorder_per_page))){
						//not last page
						$nextpage=$result['page'] + 1;						
						$this->data['msg'].="</br><a onClick='show_processing();' href='".site_url('salesorder/verify_sales_order_address/'.$nextpage)."'>Click To Verify Next ".$next_records." Sales Orders</a>";								
						$this->session->set_userdata('SOCreateDateTimeStart', $data['CreateDateTimeStart']); 
						$this->session->set_userdata('SOCreateDateTimeEnd', $data['CreateDateTimeEnd']);
						$this->session->set_userdata('total_sales_order_processed', $total_sales_order_processed);
						
					}else{
						//yes last page
						$this->session->unset_userdata('SOCreateDateTimeStart');
						$this->session->unset_userdata('SOCreateDateTimeEnd');
						$this->session->unset_userdata('total_sales_order_processed');
					}
					
					
				}else {												
					$this->data['msg'] = "Total Sales Order Found: ".$result['total_sales_orders'].", Total sales order's delivery address updated : ".$result['sales_orders_updated']."</br><a target='_blank' href='".site_url('salesorder/list_affected_sales_orders_by_api/'.$sales_order_api_call_list_id)."'>Click To view Results</a>";					
				}	
			}
			else {
				$this->data['msg'] = "No Sales Order Found";
			}
			$this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');					
			$this->load->view($this->verify_sales_order_address, $this->data);
		} else {
			die('Not Saved In Database');
		}
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

}