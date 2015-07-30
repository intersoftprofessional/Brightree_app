<?php
class Isp_Controller extends CI_Controller 
{
	var $login_status;
	var $login_url = 'admin/login';
	var $signin_view = 'signin';
	var $auto_submit_sms_gateway = false;
	var $module_name = '';
	var $model_name = '';
	/**
	 * Index Page for this controller.
	 *
	 */	 
	 
    public function __construct()
    {
          $this->data = array();
          parent::__construct();
          $this->set_login_status();
          $this->load->library('form_validation');
          $this->load->helper('cookie');
          
    }
  
  
  
    public function set_login_status()
    {
      //echo $this->session->userdata('retailer_id'); exit;
      if($this->session->userdata('retailer_id') != '')
            $this->login_status = 'true';
      else	
            $this->login_status = 'false';
    }

    public function login_check()
    {
       if($this->login_status == 'false')
       {
            redirect(site_url($this->login_url));
       }
    }
    
    //logout: redirect to the login page and session expair
    public function logout()
    {
        $a = $this->session->all_userdata();
        foreach($a as $id=>$value)
        {
            $arr[$id] = '';
        }
        //$array_items = array('user_level'=> '','name' => '','email'=> '','retailer_id'=>'');
        $this->session->unset_userdata($arr);
        $this->load->view('admin/signin');
    }
        
    function file_upload($field = '',$path='')
    {		
        //echo $path; exit;
            if(isset($path) && !empty($path)) {
            $config['upload_path'] = $path;
            }
            else {
             $config['upload_path'] = $this->file_upload_path;
            }
            //echo $config['upload_path'];
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']	= '10000';
            //$config['max_width']  = '10240';
            //$config['max_height']  = '7680';

            $this->load->library('upload', $config);
            $this->upload->upload_path =$config['upload_path'];
            if (!$this->upload->do_upload($field))
            { 
                      $error = array('error' => $this->upload->display_errors());
                      echo $error['error']; exit;
                       return false;
                     //$this->load->view('upload_form', $error);
             }
            else
            {
                    $data = array('upload_data' => $this->upload->data());
                    return $data;
                    
            }
    }   
	
	 
}
