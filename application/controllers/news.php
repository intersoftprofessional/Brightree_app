<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends Isp_Controller 
{

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
    var $module_name = 'news';
    var $list_view = 'news/list_view';
    var $model_name = 'News_Model';
    var $edit_view = 'news/edit_view';
    
    public function __construct()
    {
            parent::__construct();
            $this->load->helper('url');  
            $this->load->model($this->model_name);
            $this->login_check();
            $this->load->library('form_validation');
            // Your own constructor code
    }
    
    public function list_view($msg = '')
    {
        if($this->session->userdata('user_level') == '1') 
        {
            if($msg != '')
            {
                if($msg == 'deleted')
                    $msg = 'News successfully deleted';
                $this->data['msg'] = $msg;
            }
            
            $get_news_data = $this->{$this->model_name}->__select_all_data('headline');
            $news = $get_news_data->result_array();
            if(!empty($news))
                $this->data['beans'] = $news;
            $this->load->view($this->list_view, $this->data);
        }
        else
            echo "You have no promission to access this page.";
    }
    
    public function edit_view($news_id='', $msg = '')
    {
        if($this->session->userdata('user_level') == '1') 
        {
            if($msg != '')
            {
                if($msg == 'added')
                    $msg = 'New news successfully added to database';
                if($msg == 'edited')
                    $msg = 'News successfully edited';
                if($msg == 'deleted')
                    $msg = 'News successfully deleted';
                $this->data['msg'] = $msg;
            }
            
            if($news_id != '')
            {
                $form_data['news_id'] = $news_id;
                $get_news_info = $this->{$this->model_name}->__select($form_data);;
                $news = $get_news_info->result_array();
                if(!empty($news))
                    $this->data['bean'] = $news[0];
            }
            $this->load->view($this->edit_view, $this->data);
        }
        else
            echo "You have no promission to access this page.";
    }
    
    //here save values in the database.
    public function save()
    {
        //print_r($_POST); exit;
        $save_data=$this->input->post();
        $files = $_FILES;
        foreach($files as $key => $value)
        {
            if($value['name'] != '')
            {
                //echo $key;
                $this->file_upload_path = '';
                $this->file_upload_path = './theme/sos/drop/news/images/';
                $path = $this->file_upload_path;
                $data = $this->file_upload($key,$path);
                echo $save_data['image'][] = $PATH_SET = 'news/images/'.$data['upload_data']['file_name']; 
                
                // Include the UberGallery class
                include('./theme/sos/upload_script/resources/UberGallery.php');
                $save_pdth = $_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/news/160x115/';
                // Initialize the UberGallery object
                $gallery = new UberGallery();
                $gallery->_createThumbnail($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/theme/sos/drop/'.$PATH_SET, '160', '115',80, $save_pdth); 
            }
        } //print_r($save_data['image']); exit;
        $img = '';
        if(isset($save_data['image']))
        {
            foreach ($save_data['image'] as $id=>$value)
            {
                if($value != '')
                    $img .= $value.',';
            }
        }
        $save_data['image'] = substr($img, 0, -1);
        if(isset($save_data['news_id']) && $save_data['news_id'] != '')
        {
            $where['news_id'] = $save_data['news_id'];
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_news');
            $id = $save_data['news_id'];
            $msg = 'edited';
        }
        else
        {
            $id = $this->{$this->model_name}->__insert($save_data);
            $msg = 'added';
        }
        redirect(site_url('news/edit_view/'.$id.'/'.$msg));
        
    }
    
    public function delete($news_id = '')
    {
        if($this->session->userdata('user_level') == '1') 
        {
            $form_data['news_id'] = $news_id;
            $get_images = $this->{$this->model_name}->__select($form_data);
            $images = $get_images->result_array();
            if(!empty($images))
            {
                $imgs = $images[0]['image'];
                $img = explode(',', $imgs);
                $img_val = '';
                foreach ($img as $id=>$value)
                {
                   if(file_exists('theme/sos/drop/'.$value))
                        unlink('theme/sos/drop/'.$value);
                }
            }
            
            $this->db->delete($this->{$this->model_name}->table_name, array($this->{$this->model_name}->id_key => $news_id)); 
            redirect(site_url('news/list_view/deleted'));
        }
        else
            echo "You have no promission to access this page.";
    }
    
    public function delete_uploaded_file()
    {
        if($_POST['id'] != '')
        {
            $form_data['news_id'] = $_POST['id'];
            $get_images = $this->{$this->model_name}->__select($form_data);
            $images = $get_images->result_array();
            if(!empty($images))
            {
                $imgs = $images[0]['image'];
                $img = explode(',', $imgs);
                $img_val = '';
                foreach ($img as $id=>$value)
                {
                    if($value != $_POST['path'] )
                        $img_val .= $value.',';
                }
                $update_data['image'] = substr($img_val, 0,-1);
                $where['news_id'] = $_POST['id'];
                $this->{$this->model_name}->__update_table($where,$update_data, 'sos_news');
            }
        }
        if(file_exists('theme/sos/drop/'.$_POST['path']))
                unlink('theme/sos/drop/'.$_POST['path']);
        if($_POST['path'] != '')
        {
            $nme = explode('/', $_POST['path']);
        }
        if(file_exists('uploads/news/160x115/'.$nme[count($nme)-1]))
                unlink('uploads/news/160x115/'.$nme[count($nme)-1]);
        echo 'true';
    }

}