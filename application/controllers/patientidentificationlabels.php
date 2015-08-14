<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Patientidentificationlabels extends Isp_Controller {

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
    var $module_name = 'patientidentificationlabels';
    
    //var $model_name = 'Patientidentificationlabels_Model';    
    var $model_name = 'Patientlabels_Model'; 
	var $WIPUserTaskReason = WIPUserTaskReason; //"Ready For Shipping" WIP status of sales order

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }
	
	function index($msg='',$salesorders_inserted=0,$salesorders_updated=0 )
	{	
		$data['salesorders'] = $this->Patientlabels_Model->getSalesOrders();
		if($msg=='salesorders_updated') {
			$data['msg']='New Sales Orders Inserted: '.$salesorders_inserted.'<br> Sales Orders updated: '.$salesorders_updated;
		}
		$this->load->view('patientidentificationlabels/salesorders',$data);	
	}	
	
    public function fetch_sales_order_ready_for_shipping($id = '', $msg = '', $redirect = 'true') {
		//load library
		$this->load->library('Address_Verify');
		$result = $this->address_verify->fetch_sales_order_ready_for_shipping(array(
			'start_date' => '',
			'end_date' => '',
			'records_per_page' => 1000,
			'page' => 1,
			'WIPUserTaskReason'=>$this->WIPUserTaskReason
		));				
		        		
		$records= $this->Patientlabels_Model->updateSalesOrders($result);
		$msg='salesorders_updated';
		redirect(site_url('patientidentificationlabels/index/' . $msg.'/'.$records['inserted'].'/'.$records['updated']));
    }
}