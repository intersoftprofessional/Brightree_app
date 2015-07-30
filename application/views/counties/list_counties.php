<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo base_url(); ?>theme/sos/upload_script/css/main.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>Admin Panel</title>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/upload_script/resources/themes/uber-blue/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/upload_script/resources/colorbox/5/colorbox.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/upload_script/resources/colorbox/jquery.colorbox.js"></script>


<?php $this->load->view('admin/template/head_content'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
<script type="text/javascript">
    setTimeout(function() {
    $('#note_note').fadeOut('fast');
}, 7000);

function countcharacter(str)
{
    if (str == "")
            return 0;

    return $.trim(str).length;
}

function charcount() {
    window.charCount = 0;
     var cc = countcharacter($('#strapline').val());
     if(cc != window.charCount) {
       window.charCount = cc;
       left1 = parseInt(65) - window.charCount;
       $("#count1").html(left1+" characters left ");
     }
     else
         $("#count1").html("65 characters left ");
}

function DisableEnable(id)
{

    if ($("#closed_"+id).is(":checked"))
    {
        $( "#uniform-opening_hour_"+id ).addClass( "disabled" );
        $("#opening_hour_"+id).prop('disabled', 'disabled');
        $( "#uniform-opening_min_"+id ).addClass( "disabled" );
        $("#opening_min_"+id).prop('disabled', 'disabled');
        $( "#uniform-closing_hour_"+id ).addClass( "disabled" );
        $("#closing_hour_"+id).prop('disabled', 'disabled');
        $( "#uniform-closing_min_"+id ).addClass( "disabled" );
        $("#closing_min_"+id).prop('disabled', 'disabled');
    }
    else
    {
        $( "#uniform-opening_hour_"+id ).removeClass( "disabled" );
        $("#opening_hour_"+id).removeAttr("disabled");
        $( "#uniform-opening_min_"+id ).removeClass( "disabled" );
        $("#opening_min_"+id).removeAttr("disabled");
        $( "#uniform-closing_hour_"+id ).removeClass( "disabled" );
        $("#closing_hour_"+id).removeAttr("disabled");
        $( "#uniform-closing_min_"+id ).removeClass( "disabled" );
        $("#closing_min_"+id).removeAttr("disabled");
    }
}


</script>
<script src="<?php echo base_url(); ?>theme/sos/ckeditor/ckeditor.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>theme/sos/ckeditor/samples/sample.css">
</head>

<body onload="charcount();">


<div id="main-photo-gallery" style="display:none;">
    <a class="popupBoxClose" href="javascript:void(0);" onclick="unloadPopupBox();">x</a>
    <div class="nNote nSuccess hideit" style='display:none;' id="note_note1">
            <p><strong>SUCCESS: </strong><span id="msg_msg1"></span></p>
    </div>
    <div id="main-photo-gallery-div">
        <div><h5 id="subtitle">Upload main profile photo</h5></div>
        <div class="rowElem">
            <input type="hidden" value="profile_img" id="page" />
            <div id="dropArea"></div>
            <div class="info">
                <div>Files left: <span id="count">0</span></div>
                <h2>Result:</h2>
                <div id="result"></div>
                <canvas width="500" height="20" id="progress-bar"></canvas>
            </div>
        </div>

        <!-- Gallery -->
        <?php
            if(isset($bean['main_profile_image']) && $bean['main_profile_image'] != '')
            {
                $path_img = explode('/', $bean['main_profile_image']);
                $main_image = $path_img[count($path_img)-1];
            }
        ?>
        <div><h5 id="subtitle-gallery">Main profile photo gallery</h5></div>
        <div class="pics">
            <form method="post" action="<?php echo site_url('retailers/save_main_photo'); ?>" name="photo_upload">
                <input type="hidden" value="<?php if(isset($bean['retailer_id'])) echo $bean['retailer_id']; ?>" name="retailer_user_id" />
                <div id="galleryListWrapper">

                </div>
            </form>
        </div>

    </div>
</div>
<div id="container">
<!-- Top navigation bar -->
<div id="topNav">
	<?php $this->load->view('admin/template/topnav'); ?>
</div>

<!-- Header -->
<div id="header" class="wrapper">
	<?php $this->load->view('admin/template/header'); ?>
</div>


<!-- Content wrapper -->
<div class="wrapper clearfix">

	<!-- Left navigation -->
    <div class="leftNav">
		<?php $this->load->view('admin/template/leftnav');?>
    </div>


    <!-- Content -->
    <div class="content" id="page_title">
        <!--<div class="sample-video"><a href="http://www.soldonstourport.co.uk/vids/retailers/retailers.swf" rel="shadowbox;width=1200;height=760"><img src="<?php //echo base_url(); ?>theme/sos/images/watch-this.png" /></a></div>-->
        <div class="clear"></div>
    	<div class="title"><h5>Users</h5></div>
        <!-- Notification messages -->
        <div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
                <p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
        </div>        
        <!-- Dynamic table -->
        <div class="table">
            <div class="head"><h5 class="iFrames">Current Users</h5></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                <thead>
                    <tr>
                        <!--<th width="15%">Retailer member number</th>-->
                        <th width="3%">Sr. No.</th>			
						<th width="20%">County</th>
						<th width="20%">Active TaxZone Name</th>
						<th width="20%">Active TaxZone ID</th>
						<th width="5%">Edit</th>
                    </tr>
                </thead>
                <tbody>
                <?php $li = 1; foreach($query as $val) { ?>
                    <tr class="gradeA">
                        <!--<td><?php //echo $bean['retailer_member_number']; ?></td>-->
                        <td><?php echo $li; ?></td>
                        <td><?php echo $val->county; ?></td>
                        <td><?php echo $val->taxzone_name ?></td>
                        <td><?php echo $val->taxzone_ID; ?></td>
                        <td><?php echo '<a href="counties/edit/'.$val->ID.'">Edit</a>';?></td>

                    </tr>
                <?php $li++; } ?>
                </tbody>
            </table>
        </div>


</div>
<!-- Footer -->
<div id="footer">
	<div class="wrapper">

    </div>
</div>
</div>
<script src="<?php echo base_url(); ?>theme/sos/upload_script/js/script.js"></script>
</body>
</html>