<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Isp_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
        var $module_name = 'admin';
        var $signin_view = 'admin/signin';
        var $model_name = 'Admin_Model';
        public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');  
		$this->load->model($this->model_name);
		$this->load->library('form_validation');
                // Your own constructor code
	}
	public function index()
	{
            if($this->input->cookie('email') != '' && $this->input->cookie('password') != '')
            { 
                $this->set_cookies_session($this->input->cookie('email'), $this->input->cookie('password'));
            }
            else
                $this->load->view('admin/signin');
	}
        
        public function set_cookies_session($email, $pwd)
        {
            $form_data['email'] = $email;
            $form_data['password'] = $pwd;
            $user_data = $this->{$this->model_name}->__select($form_data);
            if($user_data->num_rows == 1)
            {
                    $admin = $user_data->result_array();
                    $this->session->set_userdata($admin[0]);
                    $a = $this->session->all_userdata();
                    if($this->session->userdata('user_level')== '1')
                        redirect(site_url('dashboard/verify_patient_address')); 
                     else
                         redirect(site_url('users/profile'));
            }
            else
                    $this->load->view('admin/signin');
        }
        
        public function login()
        {
                        //print_r($_POST); exit;
            if($this->input->post())
            {   
                    // FOR login form validation
                    $this->form_validation->set_rules('email', 'email', 'trim|required');
                    $this->form_validation->set_rules('password', 'Password', 'trim|required');
                    if ($this->form_validation->run() == FALSE)
                    {
                         $this->index();
                    } 
                    else
                    {   //print_r($_POST); exit;
                        $form_data = $_POST;
                        $form_data['email'] = $this->input->post('email');
                        $form_data['password'] = $this->input->post('password');
                        $check = $this->input->post('remember_me');
                        $data = $this->Admin_Model->__select($form_data);
                        //echo $data->num_rows; exit;
                        if($data->num_rows == 1)
                        {
                                $admin = $data->result_array(); 
                                foreach($admin[0] as $id=>$value)
                                {
                                    if($id != 'content' && $id != 'content'&& $id != 'map_code' && $id != 'tour_code' && $id != 'closing_time' && $id != 'opening_time' && $id != 'closed')
                                        $user_data[$id] = $value;
                                }
                                $this->session->set_userdata($user_data);
                                $a = $this->session->all_userdata(); 
                                //print_r($a); exit;
                                if($check == 'check')
                                { 
                                    $cookie = array(
                                                            'name'   => 'email',
                                                            'value'  => $this->input->post('email'),
                                                            'expire' => '86500',
                                                        );
                                    
                                    $cookie_1 = array(
                                                            'name'   => 'password',
                                                            'value'  => $this->input->post('password'),
                                                            'expire' => '86500',
                                                        );

                                    $this->input->set_cookie($cookie); 
                                    $this->input->set_cookie($cookie_1); 
                                }
                                else 
                                { 
                                        $cookie = array(
                                                                'name'   => 'email',
                                                                'value'  => '',
                                                                'expire' => '0',
                                                            );

                                        $cookie_1 = array(
                                                                'name'   => 'password',
                                                                'value'  => '',
                                                                'expire' => '0',
                                                            );
                                       $this->input->set_cookie($cookie);
                                       $this->input->set_cookie($cookie_1);
                                } 
                                if($this->session->userdata('user_level')== '1')
                                    redirect(site_url('dashboard/verify_patient_address')); 
                                 else
                                    redirect(site_url('users/profile'));
                        } 
                        else
                        {
                                $error['error'] = 'Your email or Password is wrong';
                                // error user name or password is wrong
                                $this->index($error);
                        }
                    }
              }
              else	
              {     
                    if($this->session->userdata('retailer_id') != '')
                    {
                         if($this->session->userdata('user_level')== '1')
                            redirect(site_url('dashboard/verify_patient_address')); 
                         else
                             redirect(site_url('users/profile')); 
                    }
                    else	 
                         $this->index();

              }
       }
       
       public function forgot_password()
       {
            $form_data['email'] = $this->input->post('email'); 
            $data = $this->Admin_Model->__select($form_data);
            if($data->num_rows > 0)
            {
                $row = $data->result_array();
                $pwd = $row[0]['password'];
                $to = $form_data['email'];
                $name = $row[0]['name'];
                $subject ='Forgotten password :';
                $message = '<html><body>';
                $message .= '<p>Hi '.$name.'</p>';
                $message .= '<p>Your password is <b>'.$pwd.'</b></p>';
                $message .= '<p>Regards,<br/>Admin</p>';
                $message .= '<p>Sold on Stourport</p>';
                $message .= '</body></html>'; 
                //end of message
                $headers  = "From: Admin<noreplay@gmail.com>\r\n";
                $headers .= "Content-type: text/html\r\n";
                mail($to, $subject, $message, $headers);
                echo 'true';
            }
            else
                echo 'false';
       }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */