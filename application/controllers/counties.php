<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Counties extends Isp_Controller {

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
    var $module_name = 'Counties';
    
    var $model_name = 'Counties_Model';    

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model($this->model_name);
        $this->login_check();
        $this->load->library('form_validation');
        // Your own constructor code
    }

    public function index() 
	{	
		$this->load->model('Counties_model');
		$data['query'] = $this->Counties_model->getCounties();
		$this->load->view('counties/list_counties',$data);
    }
	
	public function edit($id="")
	{
		$this->load->model('Counties_model');
		$data['result'] = $this->Counties_model->getCountiesTaxzones($id);		
		$this->load->view('counties/edit',$data);
	}
	
	function updatetaxzone()
	{
		$county = $this->input->post('county');
		$taxzone = $this->input->post('taxzone');
		$this->db->update('county_taxzone_mapping', array('published'=>'0'), array('county'=>$county));
		$this->db->update('county_taxzone_mapping', array('published'=>'1'), array('taxzone_ID'=>$taxzone));
		redirect('counties');		
	}
	
}