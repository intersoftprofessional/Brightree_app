<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo base_url(); ?>theme/sos/upload_script/css/main.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>SOS : Admin Panel</title>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/upload_script/resources/themes/uber-blue/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/upload_script/resources/colorbox/5/colorbox.css" />    
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/upload_script/resources/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("a[rel='colorbox']").colorbox({maxWidth: "90%", maxHeight: "90%", opacity: ".5"});
    });
</script>   
<?php $this->load->view('admin/template/head_content'); ?>
<script type="text/javascript">
    setTimeout(function() {
    $('#note_note').fadeOut('fast');
}, 3000);
</script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

</head>

<body>

<!-- Top navigation bar -->
<div id="topNav">
	<?php $this->load->view('admin/template/topnav'); ?>
</div>

<!-- Header -->
<div id="header" class="wrapper">
	<?php $this->load->view('admin/template/header'); ?>
</div>


<!-- Content wrapper -->
<div class="wrapper">
	
	<!-- Left navigation -->
    <div class="leftNav">
		<?php $this->load->view('admin/template/leftnav');?>
    </div>

    
    <!-- Content -->
    <div class="content" id="page_title">
        <div class="sample-video"><a href="http://www.soldonstourport.co.uk/vids/retailers/retailers.swf" rel="shadowbox;width=1200;height=760"><img src="<?php echo base_url(); ?>theme/sos/images/watch-this.png" /></a></div>
        <div class="clear"></div>
    	<div class="title"><h5>Upload photos</h5></div>
        <!-- Notification messages -->
        <div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
                <p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
        </div>
        <!-- Widgets -->
        <div class="fluid">
            <div class="span8">
                <fieldset>
                    <div class="widget acc first">      
                    <!-- Collapsible. Closed by default -->
                    <div class="widget">
                        <div class="head closed"><h5>Upload additional photos</h5></div>
                        <div class="body">
                           <div class="rowElem">
                               <input type="hidden" value="" id="page" />
                                <input type="hidden" id="retailer_id" value="<?php echo $this->session->userdata('retailer_id'); ?>"/>
                               <div id="dropArea">Drag and drop any additional photos you'd like to add here</div>
                               <div class="info">
                                    <div>Files left: <span id="count">0</span></div>
                                    <h2>Result:</h2>
                                    <div id="result"></div>
                                    <canvas width="500" height="20" id="progress-bar"></canvas>
                                </div>
                           </div> 
                        </div>
                    </div>

                    </div> 
                </fieldset>   
                    
            </div>
      </div>
    <!-- Gallery -->
    <?php if(isset($bean['main_image']) && $bean['main_image'] != '')
          {
            $path_img = explode('/', $bean['main_image']);
            $main_image = $path_img[count($path_img)-1];
          }
    ?>
      <div class="widget first">
          <div class="head"><h5 class="iPreview">Additional photo gallery</h5></div>
          <div class="pics">
              <form method="post" action="<?php echo site_url('retailers/save_main_photo'); ?>" name="photo_upload">
                <div id="galleryListWrapper">
                    <?php if (!empty($beans['images']) && count($beans['images']) > 0) { ?>
                          <ul id="galleryList" class="clearfix">
                              <?php $img = 1; foreach ($beans['images'] as $image){
                                  $act_path_img = explode('/', $image['file_path']);
                                  $act_image = $act_path_img[count($act_path_img)-1];
                              ?>
                                  <li>
                                      <div class="radio-pos"><input type="radio" name="main_image" value="<?php echo $image['file_path']; ?>" <?php if(isset($main_image)) if($main_image == "300x100_".$act_image) echo "checked='checked'"; ?> /></div>
                                      <div class="del_del_img">
                                          <input type="hidden" value="<?php echo $image['file_path']; ?>" id="big_image_<?php echo $img; ?>" />
                                          <input type="hidden" value="<?php echo $image['thumb_path'];?>" id="small_image_<?php echo $img; ?>"/>
                                          <a href="<?php echo base_url().$image['file_path']; ?>" title="<?php echo $image['file_title']; ?>" rel="colorbox"><img src="<?php echo base_url().$image['thumb_path']; ?>" alt="<?php echo $image['file_title']; ?>"/></a>
                                          <span class="closed" onclick="close_btn(<?php echo $img; ?>);"><img src="<?php echo base_url(); ?>theme/sos/images/closed.png" alt="delete"/></span>
                                      </div>
                                  </li>
                              <?php $img++; } ?>
                          </ul>
                          <div class="btn_submit"><input type="submit" value="Save" class="greyishBtn" /></div>

                      <?php } else { ?>
                    <div>no image found</div><?php } ?>
              </div>
            </form>
          </div>
      </div>
</div>

<!-- Footer -->
<div id="footer">
	<div class="wrapper">
    	
    </div>
</div>
<script src="<?php echo base_url(); ?>theme/sos/upload_script/js/script.js"></script>
<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
  <script type="text/javascript">
    //jQuery.noConflict ();
    function close_btn(id)
    {
        var msg = 'This will permanently delete this retailer from the interface. Are you sure?';
        new Messi(msg, {title: '', buttons: [{id: 0, label: 'Yes', val: 'Y'}, {id: 1, label: 'No', val: 'N'}], callback: function(val) {
        if(val == 'N')
        {
            return false;
        }
        else
        {
            var small = $('#small_image_'+id).val();
            var big = $('#big_image_'+id).val();
            $.ajax({ url: "<?php echo site_url('retailers/delete_image'); ?>", 
            type: "POST",
            data: { "small":small,"big":big},
            success: function(response){
            if(response != '')
            { 
                $('#galleryListWrapper').html(response);
                $('#msg_msg').html('image sucessfully deleted.');
                $('#note_note').css('display','block')
                window.location="#page_title";
                setTimeout(function() {
                $('#note_note').fadeOut('fast');
                }, 3000);
            }
            else
            {
                $('#galleryListWrapper').html('<div>no image found.</div>');
                $('#msg_msg').html('image sucessfully deleted.');
                $('#note_note').css('display','block')
                window.location="#page_title";
                setTimeout(function() {
                $('#note_note').fadeOut('fast');
                }, 3000);
            }
            }});
        }    
        }});
        return false;
    }
   </script>
</body>
</html>
