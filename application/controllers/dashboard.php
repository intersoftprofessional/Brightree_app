<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends Isp_Controller {

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
    var $module_name = 'dashboard';
    var $verify_patient_address = 'dashboard/verify_patient_address';
	var $list_affected_patients_by_api = 'dashboard/list_affected_patients_by_api';
    //var $order_view = 'offers/order_view';
    var $model_name = 'Dashboard_Model';
	var $patients_per_page = TOTAL_ADDRESSES_PROCESSED_PER_REQUEST;

    //var $edit_view = 'offers/edit_view';

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }

    public function verify_patient_address($page = 1, $msg = '', $redirect = 'true') {
        if ($this->session->userdata('user_level') == '1') {		
			
			if($msg == 'deleted')
			$this->data['msg'] = 'Patient address update request deleted successfully';
			
			//get all info from api_patient_update_call_lists table
			$table_name = 'api_patient_update_call_lists';            
            $this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');		
			
			//unset session if page 1 is called again without processing all steps
			if((! $page) || empty($page) || $page == 1) {
				$this->session->unset_userdata('PTCreateDateTimeStart');
				$this->session->unset_userdata('PTCreateDateTimeEnd');
				$this->session->unset_userdata('total_patient_processed');
			}
			
			//show view if no data is set
			if ((!isset($_POST['CreateDateTimeStart'])) && (!$this->session->userdata('PTCreateDateTimeStart'))) {
				$this->load->view($this->verify_patient_address, $this->data);
				return;
			}
			
			//set values
			$data['CreateDateTimeStart'] = (isset($_POST['CreateDateTimeStart'])) ? $_POST['CreateDateTimeStart'] : $this->session->userdata('PTCreateDateTimeStart');
			$data['CreateDateTimeEnd'] = (isset($_POST['CreateDateTimeEnd'])) ? $_POST['CreateDateTimeEnd'] : $this->session->userdata('PTCreateDateTimeEnd');            
			
			
            
            if (empty($data['CreateDateTimeStart']) || empty($data['CreateDateTimeEnd'])) {
                $this->data['msg'] = "Both Dates are required";
                $this->load->view($this->verify_patient_address, $this->data);
            } else {
			
				$this->proceed_to_verify_address($data,$page);
				/*
               echo  $this->load->library('Address_Verify');
               
                $result = $this->address_verify->patients_address_verify(array(
                    'start_date' => $_POST['CreateDateTimeStart'],
                    'end_date' => $_POST['CreateDateTimeEnd']
                ));
				
				//save patient api call lists
				$patient_api_call_list_id = $this->save_patient_api_call_lists($result,$_POST['CreateDateTimeStart'],$_POST['CreateDateTimeEnd']);
				
				//save affected patients api call lists
				if($patient_api_call_list_id) {
					if($result['total_patients']) {
						$this->save_affected_patient_lists($patient_api_call_list_id, $result['patients']);
						$this->data['msg'] = "Total Patients Found: ".$result['total_patients'].", Total Patient's address updated : ".$result['patients_updated']."</br><a target='_blank' href='".site_url('dashboard/list_affected_patients_by_api/'.$patient_api_call_list_id)."'>Click To view Results</a>";
					}
					else {
						$this->data['msg'] = "No Patients Found";
					}
					$this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');					
					$this->load->view($this->verify_patient_address, $this->data);
				}
				*/
            }
        } else
            echo "You have no permission to access this page.";
    }
	
	
	function proceed_to_verify_address($data,$page){
		$table_name = 'api_patient_update_call_lists'; 
		$total_patient_processed=0;
		//load library
		$this->load->library('Address_Verify');
               
		$result = $this->address_verify->patients_address_verify(array(
			'start_date' => $data['CreateDateTimeStart'],
			'end_date' => $data['CreateDateTimeEnd'],
			'records_per_page' => $this->patients_per_page,
			'page' => $page
		));
		
		//save patient api call lists
		$patient_api_call_list_id = $this->save_patient_api_call_lists($result,$data['CreateDateTimeStart'],$data['CreateDateTimeEnd']);
		
		//save affected patient api call lists
		if($patient_api_call_list_id) {
			if($result['total_patients']) {
				//update affected patients
				$this->save_affected_patient_lists($patient_api_call_list_id, $result['patients']);
				
				//check if more then maximum record limit records exist in the request
				if($result['total_patients'] < $result['total_patients_exist']) {
					
					$record_processed_from= (intval($result['page']) * $this->patients_per_page) -  $this->patients_per_page + 1; 
					$record_processed_to= $record_processed_from + intval($result['total_patients']) - 1;
					
					$this->data['msg'] = "Total Patients exist in your request: "
								.$result['total_patients_exist']
								."<br> Records Processed: "
								.$record_processed_from.' TO '.$record_processed_to
								.". Patients Found: "
								.$result['total_patients']
								.", Total Patient's delivery address updated : "
								.$result['patients_updated'];								
					
					$total_patient_processed = ($this->session->userdata('total_patient_processed') ? intval($this->session->userdata('total_patient_processed')) + $result['total_patients'] : $result['total_patients']);
					$remaning_records = intval($result['total_patients_exist']) - $total_patient_processed;
					$next_records = ($remaning_records < $this->patients_per_page) ? $remaning_records : $this->patients_per_page;
					
					$this->data['msg'].="</br></br>Total ".$total_patient_processed." Patients have been processed Out Of ".$result['total_patients_exist'];
					
					//check if it is a last page
					if($result['page'] != ceil((floatval($result['total_patients_exist']) / $this->patients_per_page))){
						//not last page
						$nextpage=$result['page'] + 1;
						
						$this->data['msg'].="</br><a onClick='show_processing();' href='".site_url('dashboard/verify_patient_address/'.$nextpage)
								."'>Click To Verify Next ".$next_records." Patients</a>";								
						$this->session->set_userdata('PTCreateDateTimeStart', $data['CreateDateTimeStart']); 
						$this->session->set_userdata('PTCreateDateTimeEnd', $data['CreateDateTimeEnd']);
						$this->session->set_userdata('total_patient_processed', $total_patient_processed);
						
					}else{
						//yes last page
						$this->session->unset_userdata('PTCreateDateTimeStart');
						$this->session->unset_userdata('PTCreateDateTimeEnd');
						$this->session->unset_userdata('total_patient_processed');
					}
					
					
				}else {												
					$this->data['msg'] = "Patients Found: ".$result['total_patients'].", Patients's delivery address updated : ".$result['patients_updated']."</br><a target='_blank' href='".site_url('dashboard/list_affected_patients_by_api/'.$patient_api_call_list_id)."'>Click To view Results</a>";					
				}	
			}
			else {
				$this->data['msg'] = "No Patient Found";
			}
			$this->data['beans'] = $this->get_data_from_table($table_name, 'ID' , 'DESC');					
			$this->load->view($this->verify_patient_address, $this->data);
		} else {
			die('Not Saved In Database');
		}
	}

	function save_patient_api_call_lists($data = array(),$startdate = '',$enddate='')  {
		$table_name = 'api_patient_update_call_lists';
		
		$form_data['total_patients']=$data['total_patients'];
		$form_data['patients_updated']=$data['patients_updated'];
		$form_data['patients_not_updated']=$data['patients_not_updated'];		
		$form_data['patient_create_start_date']=$startdate;
		$form_data['patient_create_end_date']=$enddate;		
		$form_data['time']= date('Y-m-d H:i:s');
		return $this->{$this->model_name}->__insert_table($form_data,$table_name);	
	}
	
	
	function save_affected_patient_lists($patient_api_call_list_id,$patients=array())  {
		$table_name = 'api_affected_patient_lists';
		
		$form_data['api_call_id']= $patient_api_call_list_id;
		if($patients && (count($patients) > 0)) {			
			foreach($patients as $patientBID => $patient) {
				$form_data['patient_brightree_id']=$patientBID;				
				$form_data['address_updated']= (isset($patient['address_update']) && $patient['address_update']) ? '1' : '0';
				$form_data['failure_message']= (isset($patient['failure_message'])) ? $patient['failure_message'] : '';
				$form_data['old_address']= ((! empty($patient['old_addr']['AddressLine1'])) ? trim($patient['old_addr']['AddressLine1']).', ' : '').
										   ((! empty($patient['old_addr']['AddressLine2'])) ? trim($patient['old_addr']['AddressLine2']).', ' : '').
										   ((! empty($patient['old_addr']['City'])) ? trim($patient['old_addr']['City']).', ' : '').
										   ((! empty($patient['old_addr']['PostalCode'])) ? trim($patient['old_addr']['PostalCode']).', ' : '').
										   //((! empty($patient['old_addr']['County'])) ? trim($patient['old_addr']['County']).', ' : '').
										   ((! empty($patient['old_addr']['State'])) ? trim($patient['old_addr']['State']) : '');
										   
				$form_data['new_address']= ((! empty($patient['new_addr']['AddressLine1'])) ? trim($patient['new_addr']['AddressLine1']).', ' : '').
										   ((! empty($patient['new_addr']['AddressLine2'])) ? trim($patient['new_addr']['AddressLine2']).', ' : '').
										   ((! empty($patient['new_addr']['City'])) ? trim($patient['new_addr']['City']).', ' : '').
										   ((! empty($patient['new_addr']['PostalCode'])) ? trim($patient['new_addr']['PostalCode']).', ' : '').
										  // ((! empty($patient['new_addr']['County'])) ? trim($patient['new_addr']['County']).', ' : '').
										   ((! empty($patient['new_addr']['State'])) ? trim($patient['new_addr']['State']) : '');	

				$form_data['patients_first_name']=trim($patient['first_name']);
				$form_data['patients_last_name']=trim($patient['last_name']);
				$form_data['patient_id']=trim($patient['patient_id']);				
				$this->{$this->model_name}->__insert_table($form_data,$table_name);
			}
		}
	}
	
	function list_affected_patients_by_api($api_call_id=0,$msg='')
	{
		if ($this->session->userdata('user_level') == '1') {
		 
			if($msg == 'deleted')
			$this->data['msg'] = 'Affected Patient information deleted successfully';
			
			//get all retailers info from sos_retailers table
			$table_name = 'api_affected_patient_lists';
			$form_data['api_call_id'] = $api_call_id;
            $get_all_affected_patients_lists = $this->{$this->model_name}->__select_table($form_data,$table_name);
            
			
			$all_affected_patients = $get_all_affected_patients_lists->result_array();
            $this->data['beans'] = $all_affected_patients;					
			$this->load->view($this->list_affected_patients_by_api, $this->data);
		}
		
	}
	
	function delete_patient_api_request($api_call_id=0)  {
		if ($this->session->userdata('user_level') == '1') {		 
			
			//delete affected patients first
			$table_name = 'api_affected_patient_lists';
			$form_data['api_call_id'] = $api_call_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name);
			
			//delete patient update requests
			$table_name = 'api_patient_update_call_lists';
			$form_data['ID'] = $api_call_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name);    
			
			$msg = 'deleted';								
			$this->verify_patient_address('',$msg);			
		}else
            echo "You have no permission to access this page.";	
	}
	
	function delete_affected_patient($affected_patient_id=0, $api_call_id=0)  {
		
		if ($this->session->userdata('user_level') == '1') {		 
			
			//get all retailers info from sos_retailers table
			$table_name = 'api_affected_patient_lists';
			$form_data['ID'] = $affected_patient_id;
            $this->{$this->model_name}->__delete_table($form_data,$table_name);    
			
			$msg = 'deleted';								
			$this->list_affected_patients_by_api($api_call_id,$msg);			
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