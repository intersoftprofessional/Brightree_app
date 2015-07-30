<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Taxzones extends Isp_Controller {

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
    var $module_name = 'taxzones';
    
    var $model_name = 'Taxzones_Model';    

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }
	
	function index($msg='',$taxzone_inserted=0,$taxzone_updated=0 )
	{
		$data['taxZones'] = $this->Taxzones_Model->getTaxZones();
		if($msg=='taxzone_updated') {
			$data['msg']='New Taxzones Inserted: '.$taxzone_inserted.'<br> Taxzones updated: '.$taxzone_updated;
		}	
		$this->load->view('counties/taxZones',$data);	
	}
	
	public function edit_taxzone($id="")
	{
		$data['result'] = $this->Taxzones_Model->getTaxZones($id);	
		$data['counties'] = $this->Taxzones_Model->getAllCounties();	
		$this->load->view('counties/edit_taxzone',$data);
	}
	
	function updatetaxzone()
	{
		$ID = $this->input->post('ID');
		$county = $this->input->post('county');
		$county2 = $this->input->post('county2');
		if(trim($county2)!='')
		{
			$data['county'] = $county2;
		}
		else
		{
			$data['county'] = $county;
		}
		$data['taxzone_ID'] = $this->input->post('taxzone_ID');
		$data['taxzone_name'] = $this->input->post('taxzone_name');
		$this->db->update('county_taxzone_mapping', $data, array('ID'=>$ID));
		redirect('taxzones');		
	}

    public function fetch_latest_taxzones($id = '', $msg = '', $redirect = 'true') {       
        $this->load->library('Common_Services');
		$result= $this->common_services->FetchTaxZones();		
		$records= $this->Taxzones_Model->updateTaxZones($result);
		$msg='taxzone_updated';
		redirect(site_url('taxzones/index/' . $msg.'/'.$records['inserted'].'/'.$records['updated']));
    }
}