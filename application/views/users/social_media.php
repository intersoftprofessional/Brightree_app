<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>SOS : Admin Panel</title>

<?php $this->load->view('admin/template/head_content'); ?>
<script type="text/javascript">
    setTimeout(function() {
    $('#note_note').fadeOut('fast');
}, 3000);
</script>
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
    	<div class="title"><h5>Social media feeds</h5></div>
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
                                    <div class="head <?php if(isset($bean['retailer_id'])) echo 'inactive'; else echo 'closed'; ?>"><h5>Edit social media links</h5></div>
                                    <div class="body">
                                        <form name="add_user" action="<?php echo site_url('retailers/profile_save'); ?>" method="post" class="mainForm" id="valid">
                                            <input type="hidden" value="<?php if(isset($bean['retailer_id'])) echo $bean['retailer_id']; ?>" name="retailer_id" id="retailer_id"/>
                                            <input type="hidden" value="social" name="redirect" />
                                            <div class="rowElem"><label>Facebook</label><div class="formRight"><input type="text" name="facebook" id="facebook" value="<?php if(isset($bean['facebook'])) echo 'http://www.facebook.com/'.$bean['facebook']; ?>" /></div></div>
                                            <div class="rowElem"><label>Twitter</label><div class="formRight"><input type="text" name="twitter" id="twitter" value="<?php if(isset($bean['twitter'])) echo 'https://twitter.com/'.$bean['twitter']; ?>" /></div></div>
                                            <div class="rowElem"><label></label><div class="formRight">
                                                <input type="submit" class="greyishBtn" value="<?php if(isset($bean['retailer_id'])) echo "Edit"; else echo "Add"; ?>" />
                                            </div></div>
                                        </form>
                                    </div>
                                </div>
                      
                                </div> 
                            </fieldset>   
                    
            </div>
      </div>
        
      
   
</div>

<!-- Footer -->
<div id="footer">
	<div class="wrapper">
    	
    </div>
</div>

</body>
</html>
