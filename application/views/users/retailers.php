<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retailers extends Isp_Controller 
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
    var $module_name = 'retailers';
    var $list_view = 'retailers/list_view';
    var $profile = 'retailers/profile';
    var $model_name = 'Retailers_Model';
    var $edit_view_page = 'retailers/page_edit_view';
    var $social_media = 'retailers/social_media';
    var $upload_photos = 'retailers/upload_photos';
    
    public function __construct()
    {
            parent::__construct();
            $this->load->helper('url');  
            $this->load->model($this->model_name);
            $this->login_check();
            $this->load->library('form_validation');
            // Your own constructor code
    }

    public function list_view($retailer_id='', $msg = '')
    { 
        if($this->session->userdata('user_level') == '1') 
        {
            //select the user info from the rp_users table.
            if($retailer_id != '' && $msg == '')
            {
                $form_data['retailer_id'] = $retailer_id;
                $get_new_retailer = $this->{$this->model_name}->__select($form_data);
                $new_retailer = $get_new_retailer->result_array();
                if(isset($new_retailer[0]))
                {
                    $this->data['bean'] = $new_retailer[0];
                }
            }

            if($msg != '')
            {
                if($msg == "added")
                    $msg = "New retaier successfully added to database";
                if($msg == "updated")
                    $msg = "Retailer successfully edited";
                if($msg == "deleted")
                    $msg = 'Retailer successfully deleted';
                $this->data['msg'] = $msg;
            }    
            //get all retailers info from sos_retailers table
            $form_all['user_level'] = "0";
            $get_all_retailers = $this->{$this->model_name}->__select($form_all);
            $all_retailers = $get_all_retailers->result_array();
            $this->data['beans'] = $all_retailers;

            $this->load->view($this->list_view, $this->data);
        }
        else
            echo "You have no promission to access this page.";
    }
    
    //here get the particuler retailer profile info
    public function profile($msg = '', $redirect = 'true')
    {
        if($this->session->userdata('user_level') == '0') 
        { 
            $form_data['retailer_id'] = $this->session->userdata('retailer_id');
            $get_new_retailer = $this->{$this->model_name}->__select($form_data);
            $new_retailer = $get_new_retailer->result_array();
            if(isset($new_retailer[0]))
            {
                $this->data['bean'] = $new_retailer[0];
            }
            if($msg != '')
            {
                if($msg == 'updated')
                    $msg = "Your profile data successfully edited";
                $this->data['msg'] = $msg;
            }
            if($redirect == "true")
                $this->load->view($this->profile, $this->data);
            else
                return $new_retailer[0];
        }
    }
    
    //here update page content
    public function page_edit_view($msg = '')
    {
        if($this->session->userdata('user_level') == '0') 
        {
            if($msg != '')
            {
                if($msg == 'updated')
                    $msg = "Page successfully edited";
                $this->data['msg'] = $msg;
            }
            $user_data = $this->profile('','false');
            $this->data['bean'] = $user_data;
            $this->load->view($this->edit_view_page, $this->data);
        }
    }
    
    //here update the social media links
    public function social_media($msg = '')
    {
        if($this->session->userdata('user_level') == '0') 
        {
            if($msg != '')
            {
                if($msg == 'updated')
                    $msg = "Social media links successfully edited";
                $this->data['msg'] = $msg;
            }
            $user_data = $this->profile('','false');
            $this->data['bean'] = $user_data;
            $this->load->view($this->social_media, $this->data);
        }
    }
    
    //here upload the photos in gallery
    public function upload_photos($msg='')
    {
        if($msg != '')
        {
            if($msg == 'updated')
                $msg = "Main image path successfully saved.";
            $this->data['msg'] = $msg;
        }
        $user_data = $this->profile('','false');
        $this->data['bean'] = $user_data;
            
        $retailer_id =$this->session->userdata('retailer_id');
        $this->file_upload_path = './uploads/gallery/'.$retailer_id;
        // Include the UberGallery class
        include('./theme/sos/upload_script/resources/UberGallery.php');

        // Initialize the UberGallery object
        $gallery = new UberGallery();

        // Initialize the gallery array
        if (file_exists($this->file_upload_path)) 
        {
            $galleryArray = $gallery->readImageDirectory($this->file_upload_path);
            $this->data["beans"] = $galleryArray;
        }
        
        $this->load->view($this->upload_photos,$this->data);
    }
    
    //here update the profile info
    public function profile_save()
    {
        foreach($_POST as $id=>$value)
        {
            if($id != 'redirect')
            {
                if($id=="content")
                {
                    if ( get_magic_quotes_gpc() )
                        $value = htmlspecialchars( stripslashes((string)$value) );
                    else
                        $value = htmlspecialchars( (string)$value );
                    $save_data[$id] = $value;
                }
                else
                    $save_data[$id] = $value;
            }
        }
        if(isset($save_data['retailer_id']) && $save_data['retailer_id'] != '')
        {
            $where['retailer_id'] = $save_data['retailer_id'];
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
            $msg = 'updated';
            if($_POST['redirect'] == 'page')
                redirect(site_url('retailers/page_edit_view/'.$msg));
            else
                if($_POST['redirect'] == 'social')
                    redirect(site_url('retailers/social_media/'.$msg));
                else
                    redirect(site_url('retailers/profile/'.$msg));
        }
    }
    
    //here save the main_image for the particuler retailer
    public function save_main_photo()
    {
        $retailer_id =$this->session->userdata('retailer_id');
        $path = $_POST['main_image'];
        // Include the UberGallery class
        include('./theme/sos/upload_script/resources/UberGallery.php');

        // Initialize the UberGallery object
        $gallery = new UberGallery();
        $main_path = $gallery->_createThumbnail($path, '300', '100'); 
        if(isset($retailer_id) && $retailer_id != '')
        {
            $save_data['main_image'] = $main_path;
            $where['retailer_id'] = $retailer_id;
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
            $msg = 'updated';
            redirect(site_url('retailers/upload_photos/'.$msg));
        }
        
    }
    
    public function delete_image()
    { 
        $user_data = $this->profile('','false');
          if(isset($user_data['main_image']) && $user_data['main_image'] != '')
          {
            $path_img = explode('/', $user_data['main_image']);
            $main_image = $path_img[count($path_img)-1];
          }
        $retailer_id =$this->session->userdata('retailer_id');
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sos/'.$_POST['big'])) 
        { 
            unlink($_SERVER['DOCUMENT_ROOT'].'/sos/'.$_POST['big']);
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sos/'.$_POST['small'])) 
        {
            unlink($_SERVER['DOCUMENT_ROOT'].'/sos/'.$_POST['small']);
        }
        
        $this->file_upload_path = './uploads/gallery/'.$retailer_id;
        // Include the UberGallery class
        include('./theme/sos/upload_script/resources/UberGallery.php');

        // Initialize the UberGallery object
        $gallery = new UberGallery();

        // Initialize the gallery array
        if (file_exists($this->file_upload_path)) 
        {
            $galleryArray = $gallery->readImageDirectory($this->file_upload_path);
        }
        ?>
        <?php    if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0): ?>
                <form method="post" action="<?php echo site_url('retailers/save_main_photo'); ?>" name="photo_upload">
               <ul id="galleryList" class="clearfix">
                            <?php $img = 1;  foreach ($galleryArray['images'] as $image){
                                $act_path_img = explode('/', $image['file_path']);
                                $act_image = $act_path_img[count($act_path_img)-1];
                            ?>
                                <li>
                                    <div class="radio-pos"><input type="radio" name="main_image" value="<?php echo $image['file_path']; ?>" <?php if(isset($main_image)) if($main_image == "300x100_".$act_image) echo "checked='checked'"; ?> /></div>
                                    <div class="del_del_img">
                                        <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                        <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                        <a  href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php $img++; } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
                </form>
           <?php endif;
          
    }
    
    //here save values in the database.
    public function save()
    {
        //print_r($_POST); exit;
        $save_data=$this->input->post();
        if((isset($save_data['email']) && isset($save_data['password']) && $save_data['email'] != '' && $save_data['password'] != '' && $save_data['password'] == $save_data['confirm_password']) || $save_data['submit'] =="Edit")
        {
            foreach($save_data as $id=>$value)
            {
                if($id != 'confirm_password' && $id != "submit")
                {
                    if($id=="content")
                    {
                        if ( get_magic_quotes_gpc() )
                                $value = htmlspecialchars( stripslashes((string)$value) );
                        else
                                $value = htmlspecialchars( (string)$value );
                        $form_data[$id] = $value;
                    }
                    else
                        $form_data[$id] = $value;
                }
            }
            if(isset($save_data['retailer_id']) && $save_data['retailer_id'] != '')
            {
                $where['retailer_id'] = $save_data['retailer_id'];
                $this->{$this->model_name}->__update_table($where, $form_data, 'sos_retailers');
                $id = $save_data['retailer_id'];
                $msg = 'updated';
            }
            else
            {
                $id = $this->{$this->model_name}->__insert($form_data);
                $msg = 'added';
            }
            redirect(site_url('retailers/list_view/'.$id.'/'.$msg));
        }
        else 
            redirect(site_url('retailers/list_view'));
    }

    //delete user from the database
    public function delete($retailer_id)
    {
        $this->db->delete($this->{$this->model_name}->table_name, array($this->{$this->model_name}->id_key => $retailer_id)); 
        redirect(site_url('retailers/list_view/'.$retailer_id.'/deleted'));
    }
     
    public function check_email_id($i = '')
    {
        if($_REQUEST['fieldId'] == 'email')
            $check = 'mail';
        else
            $check = 'member_no';
        $form_data[$_REQUEST['fieldId']] = $_REQUEST['fieldValue'];
        if($_REQUEST['retailer_id'] == '')
            $data = $this->{$this->model_name}->__select($form_data);
        else
        {
            $where = $_REQUEST['retailer_id'];
            $data = $this->{$this->model_name}->__select($form_data, $where);
        }
        if($data->num_rows > 0)
        {
            if($i != '')
            {
                echo "false"; exit;
            }
            else
                $arr = array('check'=>$check,'sucesss'=>'false','email'=>$_REQUEST['fieldValue']);
        }
        else
        {
            if($i != '')
            {
                echo "true"; exit;
            }
            else
                $arr = array('check'=>$check,'sucesss'=>'true','email'=>$_REQUEST['fieldValue']);
        }
        echo json_encode($arr);
    }
    
    public function update_password()
    {
        if($_POST['password'] == $_POST['c_pwd'])
        {
            $save_data['password'] = $_POST['password'];
            $where['retailer_id'] = $_POST['retailer_id'];
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
            echo "ok";
        }
    }
    
    public function upload()
    {
        $user_data = $this->profile('','false');
          if(isset($user_data['main_image']) && $user_data['main_image'] != '')
          {
            $path_img = explode('/', $user_data['main_image']);
            $main_image = $path_img[count($path_img)-1];
          }
        
        $retailer_id =$this->session->userdata('retailer_id');
        foreach($_FILES as $key => $value)
        {
            if($value['name'] != '')
            {
                $sFileType = $value['type'];
                $sFileSize = $this->bytesToSize1024($value['size'], 1);
                $this->file_upload_path = '';
                $this->file_upload_path = './uploads/gallery/'.$retailer_id;
                if (!file_exists($this->file_upload_path)) 
                {
                     mkdir($this->file_upload_path,0777);
                }
                $path = $this->file_upload_path;
                $data = $this->file_upload($key,$path);
                $sFileName = $data['upload_data']['file_name'];
                $this->file_upload_path = './uploads/gallery/'.$retailer_id;
                // Include the UberGallery class
                include('./theme/sos/upload_script/resources/UberGallery.php');

                // Initialize the UberGallery object
                $gallery = new UberGallery();

                // Initialize the gallery array
                if (file_exists($this->file_upload_path)) 
                {
                    $galleryArray = $gallery->readImageDirectory($this->file_upload_path);
                }
                
            }
        }
        ?>
        <?php    if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0): ?>
                <form method="post" action="<?php echo site_url('retailers/save_main_photo'); ?>" name="photo_upload">
               <ul id="galleryList" class="clearfix">
                            <?php $img = 1; foreach ($galleryArray['images'] as $image){
                                $act_path_img = explode('/', $image['file_path']);
                                $act_image = $act_path_img[count($act_path_img)-1];
                            ?>
                                <li>
                                    <div class="radio-pos"><input type="radio" name="main_image" value="<?php echo $image['file_path']; ?>" <?php if(isset($main_image)) if($main_image == "300x100_".$act_image) echo "checked='checked'"; ?> /></div>
                                    <div class="del_del_img">
                                        <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                        <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                        <a  href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
                </form>
           <?php endif; ?>
        <br/>
        <div class="s">
            <p>Your file: <?php echo $sFileName; ?> has been successfully received.</p>
            <p>Type: <?php echo $sFileType; ?></p>
            <p>Size: <?php echo $sFileSize; ?></p>
        </div>
        <?php 
        
    }
    
    public function bytesToSize1024($bytes, $precision = 2) 
    {
        $unit = array('B','KB','MB');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }
}
?>