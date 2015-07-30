<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Isp_Controller 
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
    var $module_name = 'users';
    var $list_view = 'users/list_view';
    var $profile = 'users/profile';
    var $model_name = 'Users_Model';
    var $edit_view_page = 'users/page_edit_view';
    var $social_media = 'users/social_media';
    var $upload_photos = 'users/upload_photos';
    var $upload_profile_photo = 'users/upload_profile_photo';
    
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
            if($retailer_id != '')
            {
                //$this->data['main_imgs'] = $this->upload_profile_photo('',$retailer_id,'false');
                //$this->data['images'] = $this->upload_photos('',$retailer_id,'false');
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
                    $msg = "New User successfully added to database";
                if($msg == "updated")
                    $msg = "User successfully edited";
                if($msg == "deleted")
                    $msg = 'User successfully deleted';
                if($msg == "image_save")
                    $msg = 'Image successfully saved.';
                $this->data['msg'] = $msg;
            }    
            //get all retailers info from sos_retailers table
            $form_all['user_level'] = "1";			
            $get_all_retailers = $this->{$this->model_name}->__select($form_all);
            $all_retailers = $get_all_retailers->result_array();
            $this->data['beans'] = $all_retailers;

            $this->load->view($this->list_view, $this->data);
        }
        else
            echo "You have no promission to access this page.";
    }
    
    //here get the particuler retailer profile info
    public function profile($msg = '', $redirect = 'true', $retailer_id = '')
    {
        if($retailer_id == '')
            $form_data['retailer_id'] = $this->session->userdata('retailer_id');
        else
            $form_data['retailer_id'] = $retailer_id;
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
    public function upload_photos($msg='', $redirect = 'true')
    {
        if($msg != '')
        {
            if($msg == 'updated')
                $msg = "Main image path successfully saved.";
            $this->data['msg'] = $msg;
        }
        if($redirect == 'true')
            $retailer_id =$this->session->userdata('retailer_id');
        else
            $retailer_id = $_POST['retailer_id'];
        
        $user_data = $this->profile('','false', $retailer_id);
        $this->data['bean'] = $user_data;
        //print_r($user_data); exit;
        if(isset($user_data['main_image']) && $user_data['main_image'] != '')
        {
            $path_img = explode('/', $user_data['main_image']);
            $main_image = $path_img[count($path_img)-1];
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
            $this->data["beans"] = $galleryArray;
        }
        
        if($redirect == 'true')
            $this->load->view($this->upload_photos,$this->data);
        else
        {
            if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0){ ?>
               <input type="hidden" name="type" value="" />
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
                                        <a class="cboxElement" href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php $img++; } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
           <?php } else 
                { ?>
                    <div>no image found</div><?php 
                } 
        }
    }
    
    //here upload the photos in gallery
    public function upload_profile_photo($msg='', $redirect = 'true')
    {
        if($msg != '')
        {
            if($msg == 'updated')
                $msg = "Main profile image path successfully saved.";
            $this->data['msg'] = $msg;
        }
        
        if(isset($_POST['retailer_id']) && $_POST['retailer_id'] != '')
            $retailer_id = $_POST['retailer_id'];
        else
            $retailer_id =$this->session->userdata('retailer_id');
        
        $user_data = $this->profile('','false',$retailer_id);
        $this->data['bean'] = $user_data;
        
        if(isset($user_data['main_profile_image']) && $user_data['main_profile_image'] != '')
        {
            $path_img = explode('/', $user_data['main_profile_image']);
            $main_image = $path_img[count($path_img)-1];
        }
        
        $this->file_upload_path = './uploads/gallery/'.$retailer_id.'/profile_img';
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
        
        if($redirect == 'true')
            $this->load->view($this->upload_profile_photo,$this->data);
        else
        {
            if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0){ ?>
               <input type="hidden" name="type" value="profile" />
               <ul id="galleryList" class="clearfix">
                            <?php $img = 1;  foreach ($galleryArray['images'] as $image){
                                $act_path_img = explode('/', $image['file_path']);
                                $act_image = $act_path_img[count($act_path_img)-1];
                            ?>
                                <li>
                                    <div class="radio-pos"><input type="radio" name="main_profile_image" value="<?php echo $main_image; ?>" <?php if(isset($main_image)) if($main_image == $act_image) echo "checked='checked'"; ?> /></div>
                                    <div class="del_del_img">
                                        <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                        <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                        <a class="cboxElement" href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php $img++; } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
           <?php } else 
                { ?>
                    <div>no image found</div><?php 
                } 
        }
    }
    
    //here update the profile info
    public function profile_save()
    {
        foreach($_POST as $id=>$value)
        {
            if($id != 'redirect' && $id != 'timing_sys' && 'opening' != substr($id, 0,7) && 'closing' != substr($id, 0,7) && 'closed' != substr($id, 0,6))
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
        
        if(isset($_POST['website']))
        {
            $pos = strpos($_POST['website'], 'https://');

            // Note our use of ===.  Simply == would not work as expected
            // because the position of 'a' was the 0th (first) character.
            if ($pos === false) 
            {
                $pos1 = strpos($_POST['website'], 'http://');
                if ($pos1 === false) 
                    $save_data['website'] = 'http://'.$_POST['website'];
                else
                    $save_data['website'] = $_POST['website'];
            } 
            else 
            {
                $save_data['website'] = $_POST['website'];
            }
        }
        //if(isset($_POST['business_name']))
          //  $save_data['slug_business_name'] = str_replace(' ', '-', strtolower($_POST['business_name']));
        //get facebook profile name
        if(isset($save_data['facebook']))
        {
            $facebook = $save_data['facebook'];
            if(strstr($facebook, 'facebook.com/'))
                $profile_name = explode('facebook.com/', $facebook);
            else
                if(strstr($facebook, 'facebook.com'))
                    $profile_name = explode('facebook.com', $facebook);
                else
                    $profile_name = $facebook;
            if(is_array($profile_name))
                $save_data['facebook'] = $profile_name[1];
            else
                $save_data['facebook'] = $profile_name;
        }
        
        //get twitter profile name
        if(isset($save_data['twitter']))
        {
            $twitter = $save_data['twitter'];
            if(strstr($twitter, 'twitter.com/'))
                $profile_name1 = explode('twitter.com/', $twitter);
            else
                if(strstr($twitter, 'twitter.com'))
                    $profile_name1 = explode('twitter.com', $twitter);
                else
                    $profile_name1 = $twitter;
            if(is_array($profile_name1))
                $save_data['twitter'] = $profile_name1[1];
            else
                $save_data['twitter'] = $profile_name1;
        }
        
        for($i=1; $i<=7; $i++)
        {
            if(isset($_POST['timing_sys']))
            {
                if(isset($_POST['opening_hour_'.$i]) && isset($_POST['closing_hour_'.$i]) && $_POST['opening_hour_'.$i] != '' && $_POST['closing_hour_'.$i] != '')
                {
                    $opening[$i] = $_POST['opening_hour_'.$i].':'.$_POST['opening_min_'.$i];
                    $closing[$i] = $_POST['closing_hour_'.$i].':'.$_POST['closing_min_'.$i];
                    $closed[$i] = '0';
                }
                else
                {
                    $opening[$i] = '';
                    $closing[$i] = '';
                    $closed[$i] = '1';
                }
            }
        }
        if(isset($opening)) $save_data['opening_time'] = serialize($opening);
        if(isset($closing)) $save_data['closing_time'] = serialize($closing);
        if(isset($closed)) $save_data['closed'] = serialize($closed);
        
        if(isset($save_data['retailer_id']) && $save_data['retailer_id'] != '')
        {
            $where['retailer_id'] = $save_data['retailer_id'];
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
            $msg = 'updated';
            if(isset($_POST['redirect']) && $_POST['redirect'] == 'page')
                redirect(site_url('users/page_edit_view/'.$msg));
            else
                if(isset($_POST['redirect']) && $_POST['redirect'] == 'social')
                    redirect(site_url('users/social_media/'.$msg));
                else
                    redirect(site_url('users/profile/'.$msg));
        }
    }
    
    //here save the main_image for the particuler retailer
    public function save_main_photo()
    {
        if(isset($_POST['type']) && $_POST['type'] == 'profile')
            $page = 'profile_';
        else
            $page = '';
        if(!isset($_POST['retailer_user_id']))
            $retailer_id =$this->session->userdata('retailer_id');
        else
            $retailer_id = $_POST['retailer_user_id'];
        $path = $_POST['main_'.$page.'image'];
        // Include the UberGallery class
        include('./theme/sos/upload_script/resources/UberGallery.php');

        // Initialize the UberGallery object
        $gallery = new UberGallery();
        if($page == '')
            $main_path = $gallery->_createThumbnail($path, '300', '100'); 
        if(isset($_POST['type']) && $_POST['type'] == 'profile')
            $main_path = $path;
        
        //$main_path1 = $gallery->_createThumbnail($path, '208', '139'); 
        if(isset($retailer_id) && $retailer_id != '')
        {
            $save_data['main_'.$page.'image'] = $main_path;
            $where['retailer_id'] = $retailer_id;
            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
            $msg = 'updated';
            if(!isset($_POST['retailer_user_id']))
            {
                if($page == '')
                    redirect(site_url('users/upload_photos/'.$msg));
                else
                    redirect(site_url('users/upload_profile_photo/'.$msg));
            }
            else
                redirect(site_url('users/list_view/'.$retailer_id.'/image_save'));
        }
        
    }
    
    public function delete_image()
    { 
        $type = '';
        if(isset($_POST['page']) && $_POST['page']== 'profile_img')
        {
            $page = '/'.$_POST['page'];
            $type = 'profile';
        }
        else
            $page ='';
        if($type != '') $type1 = $type.'_'; else $type1 = ''; 
        if(!isset($_POST['retailer_id']))
            $retailer_id =$this->session->userdata('retailer_id');
        else
            $retailer_id = $_POST['retailer_id'];
        
        $user_data = $this->profile('','false',$retailer_id);  
          if(isset($user_data['main_'.$type1.'image']) && $user_data['main_'.$type1.'image'] != '')
          {
            $path_img = explode('/', $user_data['main_'.$type1.'image']);
            $rel_img = $main_image = $path_img[count($path_img)-1];
            $img_ext = explode('.', $main_image);
            $ext = $img_ext[count($img_ext)-1];
            $main_image = '';
          }
          $chk_img = explode('/', $_POST['big']);
         if('300x100_'.$chk_img[count($chk_img)-1] == $main_image || $chk_img[count($chk_img)-1] == $rel_img)
         {
             $save_data['main_'.$type1.'image'] = '';
             $where['retailer_id'] = $retailer_id;
             $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
         }
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/'.$_POST['big'])) 
        { 
            unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/'.$_POST['big']);
        }
        if($type!= '')
        {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/181x97/'.$rel_img)) 
            { 
                unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/181x97/'.$rel_img);
            }
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/208x139/'.$rel_img)) 
            { 
                unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/208x139/'.$rel_img);
            }
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/630x420/'.$rel_img)) 
            { 
                unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/630x420/'.$rel_img);
            }
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/'.$_POST['small'])) 
        {
            unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/'.$_POST['small']);
        }
        
        $this->file_upload_path = './uploads/gallery/'.$retailer_id.$page;
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
               <input type="hidden" name="type" value="<?php echo $type; ?>" />
               <ul id="galleryList" class="clearfix">
                            <?php $img = 1;  foreach ($galleryArray['images'] as $image){
                                $act_path_img = explode('/', $image['file_path']);
                                $act_image = $act_path_img[count($act_path_img)-1];
                            ?>
                                <li>
                                    <div class="radio-pos"><input type="radio" name="main_<?php echo $type1; ?>image" value="<?php echo $image['file_path']; ?>" <?php if(isset($main_image)) if($main_image == "300x100_".$act_image) echo "checked='checked'"; ?> /></div>
                                    <div class="del_del_img">
                                        <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                        <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                        <a class="cboxElement" href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php $img++; } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
           <?php endif;
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
                $this->file_upload_path = './uploads/users/';
                $path = $this->file_upload_path;
                $data = $this->file_upload($key,$path);
                $save_data['email_name_img'] = $data['upload_data']['file_name'];
            }
        }
        
        //get facebook profile name
        $facebook = $save_data['facebook'];
        if(strstr($facebook, 'facebook.com/'))
            $profile_name = explode('facebook.com/', $facebook);
        else
            if(strstr($facebook, 'facebook.com'))
                $profile_name = explode('facebook.com', $facebook);
            else
                $profile_name = $facebook;
        if(is_array($profile_name))
            $save_data['facebook'] = $profile_name[1];
        else
            $save_data['facebook'] = $profile_name;
        
        //get twitter profile name
        $twitter = $save_data['twitter'];
        if(strstr($twitter, 'twitter.com/'))
            $profile_name1 = explode('twitter.com/', $twitter);
        else
            if(strstr($twitter, 'twitter.com'))
                $profile_name1 = explode('twitter.com', $twitter);
            else
                $profile_name1 = $twitter;
        if(is_array($profile_name1))
            $save_data['twitter'] = $profile_name1[1];
        else
            $save_data['twitter'] = $profile_name1;
        
        if((isset($save_data['email']) && isset($save_data['password']) && $save_data['email'] != '' && $save_data['password'] != '' && $save_data['password'] == $save_data['confirm_password']) || $save_data['submit'] =="Edit")
        {
            foreach($save_data as $id=>$value)
            { 
                if($id != 'confirm_password' && $id != "submit" && 'opening' != substr($id, 0,7) && 'closing' != substr($id, 0,7) && 'closed' != substr($id, 0,6))
                {
                    if($id=="content")
                    {
                        if ( get_magic_quotes_gpc() )
                                $value = htmlspecialchars( stripslashes((string)$value) );
                        else
                                $value = htmlspecialchars( (string)$value );
                        $form_data[$id] = $value;
                    }
                    else if($id=="slug_business_name")
                        $form_data[$id] = strtolower($value);
                    else
                        $form_data[$id] = $value;
                }
            }
            if($form_data['slug_business_name'] == '')
                $form_data['slug_business_name'] = str_replace(' ', '-', strtolower($save_data['business_name']));
            
            $pos = strpos($save_data['website'], 'https://');

            // Note our use of ===.  Simply == would not work as expected
            // because the position of 'a' was the 0th (first) character.
            if ($pos === false) 
            {
                $pos1 = strpos($save_data['website'], 'http://');
                if ($pos1 === false) 
                    $form_data['website'] = 'http://'.$save_data['website'];
                else
                    $form_data['website'] = $save_data['website'];
            } 
            else 
            {
                $form_data['website'] = $save_data['website'];
            }
            
            for($i=1; $i<=7; $i++)
            {
                if(isset($save_data['opening_hour_'.$i]) && $save_data['opening_hour_'.$i] != '' && isset($save_data['closing_hour_'.$i]) && $save_data['closing_hour_'.$i] != '')
                {
                    $opening[$i] = $save_data['opening_hour_'.$i].':'.$save_data['opening_min_'.$i];
                    $closing[$i] = $save_data['closing_hour_'.$i].':'.$save_data['closing_min_'.$i];
                    $closed[$i] = '0';
                }
                else
                {
                    $opening[$i] = '';
                    $closing[$i] = '';
                    $closed[$i] = '1';
                }
            }
            $form_data['opening_time'] = serialize($opening);
            $form_data['closing_time'] = serialize($closing);
            $form_data['closed'] = serialize($closed);
            //print_r($form_data); exit;
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
            redirect(site_url('users/list_view/'.$id.'/'.$msg));
        }
        else 
            redirect(site_url('users/list_view'));
    }

    //delete user from the database
    public function delete($retailer_id)
    {		
        $this->db->delete($this->{$this->model_name}->table_name, array($this->{$this->model_name}->id_key => $retailer_id)); 
        redirect(site_url('users/list_view/'.$retailer_id.'/deleted'));
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
    
    public function upload($page = '',$retailer_id='')
    {
        // Include the UberGallery class
        include('./theme/sos/upload_script/resources/UberGallery.php');
        // Initialize the UberGallery object
        $gallery = new UberGallery();
        
        if($page == 'profile_img')
            $type= 'profile';
        else
        {
            $type = '';
            $page = '';
        }
        if($type != '') $type1 = $type.'_'; else $type1 = '';
        //echo $page; exit;
        $user_data = $this->profile('','false',$retailer_id);
          if(isset($user_data['main_'.$type1.'image']) && $user_data['main_'.$type1.'image'] != '')
          {
            $path_img = explode('/', $user_data['main_'.$type1.'image']);
            $main_image = $path_img[count($path_img)-1];
          }
        if($retailer_id == '')
            $retailer_id =$this->session->userdata('retailer_id');
        
        $image_info = getimagesize($_FILES["myfile"]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if(($page == 'profile_img' && $image_width > ($image_height*120/100)) || $page == '')
        {
            if(($page == 'profile_img' && $image_width >= '630') || $page == '')
            {
                $msg = 'ok';
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
                             if($page == 'profile_img')
                                mkdir($this->file_upload_path.'/'.$page,0777);
                        }
                        else if($page == 'profile_img')
                        {
                            if (!file_exists($this->file_upload_path.'/'.$page)) 
                                mkdir($this->file_upload_path.'/'.$page,0777);

                        }

                        $path = $this->file_upload_path.'/'.$page;
                        $data = $this->file_upload($key,$path);
                        $sFileName = $data['upload_data']['file_name'];
                        if($page == 'profile_img' && $sFileName != '')
                        { 
                            if(isset($main_image) && $main_image != '')
                            {
                                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/'.$main_image)) 
                                { 
                                    unlink($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/'.$main_image);
                                }
                            }
                            $save_data['main_'.$type1.'image'] = $sFileName;
                            $where['retailer_id'] = $retailer_id;
                            $this->{$this->model_name}->__update_table($where, $save_data, 'sos_retailers');
                            
                            $PATH_SET = $_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/'.$sFileName;
                            if(file_exists($PATH_SET)) 
                            { 
                                $save_pdth = $_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/gallery/'.$retailer_id.'/profile_img/';
                                    if (!file_exists($save_pdth.'630x420')) 
                                        mkdir($save_pdth.'630x420',0777);
                                    if (!file_exists($save_pdth.'181x97')) 
                                        mkdir($save_pdth.'181x97',0777);
                                    if (!file_exists($save_pdth.'208x139')) 
                                        mkdir($save_pdth.'208x139',0777);
                                    $gallery->_createThumbnail($PATH_SET, '630', '420',80, $save_pdth.'630x420',$retailer_id); 
                                    $gallery->_createThumbnail($PATH_SET, '181', '97',80,$save_pdth.'181x97',$retailer_id); 
                                    $gallery->_createThumbnail($PATH_SET, '208', '139',80,$save_pdth.'208x139',$retailer_id); 
                                
                            }
                        }
                    }
                }
            }
            else $msg = 'width_error';
        }
        else $msg = 'landscape_error';
        
        $this->file_upload_path = './uploads/gallery/'.$retailer_id.'/'.$page;
        // Initialize the gallery array
        if (file_exists($this->file_upload_path)) 
        {
            $galleryArray = $gallery->readImageDirectory($this->file_upload_path);
        }
        ?>
        <?php    if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0){ ?>
                <input type="hidden" name="type" value="<?php echo $type; ?>" />
               <ul id="galleryList" class="clearfix">
                            <?php $img = 1; foreach ($galleryArray['images'] as $image){
                                $act_path_img = explode('/', $image['file_path']);
                                $act_image = $act_path_img[count($act_path_img)-1];
                            ?>
                                <li>
                                    <div class="radio-pos"><input type="radio" name="main_<?php echo $type1; ?>image" value="<?php if($page == 'profile_img'){ if(isset($sFileName)) echo $sFileName; else echo $main_image; } else echo $image['file_path']; ?>" <?php if($page == 'profile_img') echo "checked='checked'"; else if(isset($main_image)) if($main_image == "300x100_".$act_image) echo "checked='checked'"; ?> /></div>
                                    <div class="del_del_img">
                                        <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                        <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                        <a class="cboxElement" href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                        <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>
           <?php } else { ?><div>no image found</div><?php } ?>
        <br/><?php echo $msg; ?><br/>
        <?php if(isset($msg) && $msg == 'landscape_error') { ?>
        <div class="s" style="background: #E79696;">
            <p>Your file: Your profile picture needs to be in landscape format.</p>
        </div>
        <?php    
        } else
        if(isset($msg) && $msg == 'width_error') { ?>
        <div class="s" style="background: #E79696;">
            <p>Your file: Your profile picture needs to be at least 630px wide. Please select a larger picture.</p>
        </div>
        <?php    
        }
        else { ?>
        <div class="s">
            <p>Your file: <?php echo $sFileName; ?> has been successfully received.</p>
            <p>Type: <?php echo $sFileType; ?></p>
            <p>Size: <?php echo $sFileSize; ?></p>
         </div>
        <?php }
    }
    
    public function bytesToSize1024($bytes, $precision = 2) 
    {
        $unit = array('B','KB','MB');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }
}
?>