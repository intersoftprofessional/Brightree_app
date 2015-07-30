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

<script type="text/javascript">
    $(document).ready(function(){
        $("a[rel='colorbox']").colorbox({maxWidth: "90%", maxHeight: "90%", opacity: ".5"});
    });
</script>

<script type="text/javascript">
    function upload_photo()
    {
        $('#subtitle').html('Upload additional photos');
        $('#subtitle-gallery').html('Current additional photos');
        $('#page').val('img');
        $('#result').html('');
        $('#progress-bar').css('display','none');
        $('#dropArea').html('Drag and drop any additional photos you\'d like to add here');
        <?php if(isset($bean['retailer_id'])) $ret_id = $bean['retailer_id']; else $ret_id =''; ?>
        var retailer_id = "<?php echo $ret_id; ?>";
        $.ajax({ url: "<?php echo site_url('users/upload_photos/abc/false'); ?>",
        type: "POST",
        data: { "retailer_id":retailer_id},
        success: function(response){
        if(response != '')
            $('#galleryListWrapper').html(response);
        else
            $('#galleryListWrapper').html('<div>no image found.</div>');
        }});
        $('#main-photo-gallery').fadeIn("slow");
        $("#container").css({ // this is just for style
                                    "opacity": "0.3"
                            });
    }

    function upload_main_photo()
    {
        $('#subtitle').html('Upload main profile photo');
        $('#subtitle-gallery').html('Main profile photo gallery');
        $('#page').val('profile_img');
        $('#result').html('');
        $('#progress-bar').css('display','none');
        $('#dropArea').html('Drag and drop your profile image here. The image must be landscape (not portrait) and at lease 630px wide');
        <?php if(isset($bean['retailer_id'])) $ret_id = $bean['retailer_id']; else $ret_id =''; ?>
        var retailer_id = "<?php echo $ret_id; ?>";
        $.ajax({ url: "<?php echo site_url('users/upload_profile_photo/abc/false'); ?>",
        type: "POST",
        data: { "retailer_id":retailer_id},
        success: function(response){
        if(response != '')
            $('#galleryListWrapper').html(response);
        else
            $('#galleryListWrapper').html('<div>no image found.</div>');
        }});
        $('#main-photo-gallery').fadeIn("slow");
        $("#container").css({ // this is just for style
                                    "opacity": "0.3"
                            });
    }

    // When site loaded, load the Popupbox First
    function unloadPopupBox()
    {	// To Load the Popupbox
        $('#main-photo-gallery').fadeOut("slow");
        $("#container").css({ // this is just for style
                                    "opacity": "1"
                            });
    }
</script>

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
        <!-- Widgets -->
        <!--<div class="fluid">
            <div class="span8">
                            <fieldset>
                                <div class="widget acc first">-->
                                <!-- Collapsible. Closed by default -->
                                <!--<div class="widget">
                                    <div class="head <?php /* if(isset($bean['retailer_id'])) echo 'inactive'; else echo 'closed'; ?>"><h5>Add/Edit new retailer</h5></div>
                                    <div class="body">
                                        <form name="add_user" action="<?php echo site_url('retailers/save'); ?>" method="post" class="mainForm" id="valid" enctype="multipart/form-data">
                                            <input type="hidden" value="<?php if(isset($bean['retailer_id'])) echo $bean['retailer_id']; ?>" name="retailer_id" id="retailer_id"/>
                                            <input type="hidden" value="" id="h_check" />
                                            <?php if(isset($bean['password'])) $pwd = $bean['password']; else $pwd = ''; ?>
                                            <div class="rowElem"><label>Retailer member number</label><div class="formRight"><input type="text" value="<?php if(isset($bean['retailer_member_number'])) echo $bean['retailer_member_number']; ?>" name="retailer_member_number" id="retailer_member_number" class="validate[required,ajax[check_email_address]]" autocomplete="off" /></div></div>
                                            <div class="rowElem"><label>Name</label><div class="formRight"><input type="text" name="name" id="name" value="<?php if(isset($bean['name'])) echo $bean['name']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Business name</label><div class="formRight"><input type="text" name="business_name" id="business_name" value="<?php if(isset($bean['business_name'])) echo $bean['business_name']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Industry</label><div class="formRight"><input type="text" name="industry" id="industry" value="<?php if(isset($bean['industry'])) echo $bean['industry']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>URL name</label><div class="formRight"><input type="text" name="slug_business_name" id="slug_business_name" value="<?php if(isset($bean['slug_business_name'])) echo $bean['slug_business_name']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Strapline</label><div class="formRight"><textarea name="strapline" id="strapline" onkeyup="charcount();" maxlength="65" onblur="charcount();"/><?php if(isset($bean['strapline'])) echo $bean['strapline']; ?></textarea><div id="count1">65 characters left</div></div></div>
                                            <div class="rowElem"><label>Address line 1</label><div class="formRight"><input type="text" name="address_1" id="address_1" value="<?php if(isset($bean['address_1'])) echo $bean['address_1']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Address line 2</label><div class="formRight"><input type="text" name="address_2" id="address_2" value="<?php if(isset($bean['address_2'])) echo $bean['address_2']; ?>" /></div></div>
                                            <div class="rowElem"><label>Town</label><div class="formRight"><input type="text" name="town" id="town" value="<?php if(isset($bean['town'])) echo $bean['town']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Postcode</label><div class="formRight"><input type="text" name="postcode" id="postcode" value="<?php if(isset($bean['postcode'])) echo $bean['postcode']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Phone number 1</label><div class="formRight"><input type="text" name="phone_number_1" id="phone_number_1" value="<?php if(isset($bean['phone_number_1'])) echo $bean['phone_number_1']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Phone number 2</label><div class="formRight"><input type="text" name="phone_number_2" id="phone_number_2" value="<?php if(isset($bean['phone_number_2'])) echo $bean['phone_number_2']; ?>" /></div></div>
                                            <div class="rowElem"><label>Email</label><div class="formRight"><input type="text" name="email" id="email" value="<?php if(isset($bean['email'])) echo $bean['email']; ?>" class="validate[required,custom[email], ajax[check_email_address]]" autocomplete="off"/></div></div>
                                            <div class="rowElem">
                                                <label>Email name image</label>
                                                <div class="formRight">
                                                    <input type="file" name="file" id="file" style='position: absolute;'/><img src='<?php echo base_url(),'uploads/retailers/'.$bean['email_name_img']; ?>' style='margin-top:10px;' />
                                                </div>
                                            </div>
                                            <div class="rowElem"><label>Show email?</label><div class="formRight"><input type="radio" name="show_email" id="radio1" value="0" <?php if(!isset($bean['show_email']) || (isset($bean['show_email']) && $bean['show_email'] == '0')) echo 'checked'; ?> /><label for="radio1">No</label><input type="radio" name="show_email" id="radio2" value="1" <?php if(isset($bean['show_email']) && $bean['show_email'] == '1') echo 'checked'; ?> /><label for="radio2">Yes</label></div></div>
                                            <div class="rowElem"><label>Website</label><div class="formRight"><input type="text" name="website" id="website" value="<?php if(isset($bean['website'])) echo $bean['website']; ?>" /></div></div>
                                            <?php if(!isset($bean['retailer_id'])) { ?>
                                            <div class="rowElem"><label>Password</label><div class="formRight"><input type="password" name="password" id="password" value="<?php if(isset($bean['password'])) echo $bean['password']; ?>" class="validate[required]"/></div></div>
                                            <div class="rowElem"><label>Confirm password</label><div class="formRight"><input type="password" name="confirm_password" id="confirm_password" value="<?php if(isset($bean['password'])) echo $bean['password']; ?>" class="validate[required,equals[password]]"/></div></div>
                                            <?php } ?>
                                            <?php if(isset($bean['retailer_id'])) { ?>
                                            <div class="rowElem">
                                                <label>Gallery</label>
                                                <div class="formRight">
                                                    <input type="button" class="blueBtn" value="Upload main profile photo" onclick="upload_main_photo();" />
                                                    <input type="button" value="Upload additional photos" class="blueBtn" style="margin-left:40px;" onclick="upload_photo();"/>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <input type="hidden" value="<?php if(isset($bean['password'])) echo $bean['password']; ?>" id="chk-pwd" />
                                            <div class="rowElem"><label>Content</label><div class="formRight"><textarea name="content" class="ckeditor" id="editor1"/><?php if(isset($bean['content'])) echo $bean['content']; ?></textarea></div></div>
                                            <div class="rowElem"><label>Meta description</label><div class="formRight"><textarea name="meta_description" /><?php if(isset($bean['meta_description'])) echo $bean['meta_description']; ?></textarea></div></div>
                                            <div class="rowElem"><label>Meta keywords</label><div class="formRight"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php if(isset($bean['meta_keywords'])) echo $bean['meta_keywords']; ?>" /></div></div>
                                            <div class="rowElem"><label>Title bar</label><div class="formRight"><input type="text" name="titlebar" id="titlebar" value="<?php if(isset($bean['titlebar'])) echo $bean['titlebar']; ?>" /></div></div>
                                            <div class="rowElem"><label>Facebook</label><div class="formRight"><input type="text" name="facebook" id="facebook" value="<?php if(isset($bean['facebook'])) echo 'http://www.facebook.com/'.$bean['facebook']; ?>" /></div></div>
                                            <div class="rowElem"><label>Twitter</label><div class="formRight"><input type="text" name="twitter" id="twitter" value="<?php if(isset($bean['twitter'])) echo 'https://twitter.com/'.$bean['twitter']; ?>" /></div></div>
                                            <div class="rowElem"><label>Map code</label><div class="formRight"><textarea name="map_code" id="map_code" /><?php if(isset($bean['map_code'])) echo $bean['map_code']; ?></textarea></div></div>
                                            <div class="rowElem"><label>Tour code</label><div class="formRight"><textarea name="tour_code" id="tour_code"/><?php if(isset($bean['tour_code'])) echo $bean['tour_code']; ?></textarea></div></div>
                                            <div id="user-opening-closing-time">
                                                <div class="rowElem">
                                                    <div class="formRight">
                                                        <div class="timeingdiv"><strong>Opening time</strong></div>
                                                        <div class="timeingdiv"><strong>Closing time</strong></div>
                                                        <div class="closeddiv"><strong>Closed</strong></div>
                                                    </div>
                                                </div>
                                                <?php
                                                if(isset($bean['opening_time'])) $opening = unserialize($bean['opening_time']);
                                                if(isset($bean['closing_time'])) $closing = unserialize($bean['closing_time']);
                                                if(isset($bean['closed'])) $closed = unserialize($bean['closed']);
                                                $days = array('1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday','7'=>'Sunday');
                                                foreach ($days as $id=>$value)
                                                {
                                                    if(isset($opening[$id])) $op_time = explode(':', $opening[$id]);
                                                    if(isset($closing[$id])) $cl_time = explode(':', $closing[$id]);
                                                ?>
                                                <div class="rowElem">
                                                    <label><?php echo $value; ?></label>
                                                    <div class="formRight">
                                                        <div class="timeingdiv">
                                                            <div class="time-select-box">
                                                                <select name="opening_hour_<?php echo $id; ?>" id="opening_hour_<?php echo $id; ?>" class="styled" <?php if(isset($closed[$id]) && $closed[$id] == '1') echo 'disabled'; ?>>
                                                                    <option value="">Hrs</option>
                                                                    <?php
                                                                    for ($i=1; $i<=12; $i++)
                                                                    {
                                                                        if(strlen($i) == '1')
                                                                            $h = '0'.$i;
                                                                        else
                                                                            $h = $i;
                                                                        ?>
                                                                    <option value="<?php echo $h; ?>" <?php if(isset($op_time[0]) && $op_time[0] == $h) echo 'selected'; ?>><?php echo $h; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="time-select-box mgn-left30px">
                                                                <select name="opening_min_<?php echo $id; ?>" id="opening_min_<?php echo $id; ?>" class="styled" <?php if(isset($closed[$id]) && $closed[$id] == '1') echo 'disabled'; ?>>
                                                                    <option value="">Mins</option>
                                                                    <?php
                                                                    $mins = array('00','05','10','15','20','25','30','35','40','45','50','55');
                                                                    foreach ($mins as $id1=>$value1)
                                                                    {
                                                                        ?>
                                                                    <option value="<?php echo $value1; ?>"  <?php if(isset($op_time[1]) && $op_time[1] == $value1) echo 'selected'; ?>><?php echo $value1; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="timeingdiv">
                                                            <div class="time-select-box">
                                                                <select name="closing_hour_<?php echo $id; ?>" id="closing_hour_<?php echo $id; ?>" class="styled" <?php if(isset($closed[$id]) && $closed[$id] == '1') echo 'disabled'; ?>>
                                                                    <option value="">Hrs</option>
                                                                    <?php
                                                                    for ($i=1; $i<=12; $i++)
                                                                    {
                                                                        if(strlen($i) == '1')
                                                                            $h = '0'.$i;
                                                                        else
                                                                            $h = $i;
                                                                        ?>
                                                                    <option value="<?php echo $h; ?>" <?php if(isset($cl_time[0]) && $cl_time[0] == $h) echo 'selected'; ?>><?php echo $h; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="time-select-box mgn-left30px">
                                                                <select name="closing_min_<?php echo $id; ?>" id="closing_min_<?php echo $id; ?>" class="styled" <?php if(isset($closed[$id]) && $closed[$id] == '1') echo 'disabled'; ?>>
                                                                    <option value="">Mins</option>
                                                                    <?php
                                                                    $mins = array('00','05','10','15','20','25','30','35','40','45','50','55');
                                                                    foreach ($mins as $id1=>$value1)
                                                                    {
                                                                        ?>
                                                                    <option value="<?php echo $value1; ?>"  <?php if(isset($cl_time[1]) && $cl_time[1] == $value1) echo 'selected'; ?>><?php echo $value1; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="closeddiv">
                                                            <input type="checkbox" name="closed_<?php echo $id; ?>" id="closed_<?php echo $id; ?>" value="1" onclick="DisableEnable(<?php echo $id; ?>);" <?php if(isset($closed[$id]) && $closed[$id] == '1') echo 'checked'; ?> />
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div class="rowElem"><label></label><div class="formRight">
                                                <input type="submit" name="submit" class="greyishBtn" value="<?php if(isset($bean['retailer_id'])) echo "Edit"; else echo "Add"; ?>" />
                                                <?php if(isset($bean["retailer_id"])){ ?><input type="button" value="change password" class="greyishBtn" style="margin-left:40px;" onclick="return change_pwd();"/><?php } */?>
                                            </div></div>
                                        </form>
                                    </div>
                                </div>

                                </div>
                            </fieldset>

            </div>
      </div>-->
        <!-- Dynamic table -->
        <div class="table">
            <div class="head"><h5 class="iFrames">Current Users</h5></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                <thead>
                    <tr>
                        <!--<th width="15%">Retailer member number</th>-->
                        <th width="25%">Name</th>
                        <th width="15%">Phone number</th>
                        <th width="30%">Email</th>
                        <th width="25%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php $li = 0; foreach($beans as $bean) { ?>
                    <tr class="gradeA">
                        <!--<td><?php //echo $bean['retailer_member_number']; ?></td>-->
                        <td><?php echo $bean['name']; ?></td>
                        <td><?php echo $bean['phone_number_1']; ?></td>
                        <td><?php echo $bean['email']; ?></td>
                        <td class="center"><div class="num">
                        	<!--<a href="<?php echo site_url('retailers/list_view/'.$bean['retailer_id']); ?>" class="greenNum">
                            <img alt="" src="<?php echo base_url(); ?>theme/sos/images/editgrey.1.png">
                            </a>--><input type="hidden" value="<?php echo site_url('users/delete/'.$bean['retailer_id']); ?>" id="link_del_<?php echo $li; ?>"/>
                            <a href="javascript:void(0);" onclick="return close_btn1(<?php echo $li; ?>,<?php echo $bean['retailer_id']; ?>);" class="greenNum">
                            <img alt="" src="<?php echo base_url(); ?>theme/sos/images/icons/dark/close.png" />
                            </a>
                            </div>
                       </td>

                    </tr>
                <?php $li++; } ?>
                </tbody>
            </table>
        </div>


</div>
<script type="text/javascript">
$("#email,#retailer_member_number").keyup(function(){
    var fieldId = this.id;
    var fieldValue = this.value;
    var retailer_id = $('#retailer_id').val();
    $.ajax({ url: "<?php echo site_url('retailers/check_email_id/1'); ?>",
            type: "POST",
            data: { "fieldId":fieldId,"fieldValue":fieldValue,"retailer_id": retailer_id },
            success: function(response){
            if(response== "true")
            {
                $("#h_check").val("true");
            }
            else
                $("#h_check").val("false");
        }});
  });
</script>
<!--<script src="<?php echo base_url(); ?>theme/sos/messi/jquery-1.7.2.js"></script>-->
<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
  <script type="text/javascript">
    //jQuery.noConflict ();
    function close_btn1(id, retailer_id)
    {
		var current_user_id= "<?php echo $this->session->userdata['retailer_id']; ?>";
		
		if(retailer_id == current_user_id) {
			var msg = 'Sorry !!! You can not delete yourself';
			new Messi(msg, {title: 'Alert', buttons: [{id: 0, label: 'OK', val: 'Y'}], callback: function(val) {
				return false;
			}});
			return false;
		}
		
        var msg = 'This will permanently delete this user. Are you sure?';
        new Messi(msg, {title: '', buttons: [{id: 0, label: 'Yes', val: 'Y'}, {id: 1, label: 'No', val: 'N'}], callback: function(val) {
        if(val == 'N')
        {
            return false;
        }
        else
        {
            var link = document.getElementById('link_del_'+id).value;
            window.location = link;
        }
        }});
        return false;
    }
   </script>
<!-- Footer -->
<div id="footer">
	<div class="wrapper">

    </div>
</div>
</div>
<script src="<?php echo base_url(); ?>theme/sos/upload_script/js/script.js"></script>
<script type="text/javascript">

    function close_btn(id)
    {
        var page = $('#page').val();
        var msg = 'This will permanently delete this retailer from the interface. Are you sure?';
        new Messi(msg, {title: '', buttons: [{id: 0, label: 'Yes', val: 'Y'}, {id: 1, label: 'No', val: 'N'}], callback: function(val) {
        if(val == 'N')
        {
            return false;
        }
        else
        {
            var retailer_id = $('#retailer_id').val();
            var small = $('#small_image_'+id).val();
            var big = $('#big_image_'+id).val();
            $.ajax({ url: "<?php echo site_url('retailers/delete_image'); ?>",
            type: "POST",
            data: { "retailer_id":retailer_id,"page":page,"small":small,"big":big},
            success: function(response){
            if(response != '')
            {
                $('#galleryListWrapper').html(response);
                $('#msg_msg1').html('image sucessfully deleted.');
                $('#note_note1').css('display','block')
                setTimeout(function() {
                $('#note_note1').fadeOut('fast');
                }, 3000);
            }
            else
            {
                $('#galleryListWrapper').html('<div>no image found.</div>');
                $('#msg_msg1').html('image sucessfully deleted.');
                $('#note_note1').css('display','block')
                setTimeout(function() {
                $('#note_note1').fadeOut('fast');
                }, 3000);
            }
            }});
        }
        }});
        return false;
    }

  //jQuery.noConflict ();
  function change_pwd()
  {
      var pwd = document.getElementById('chk-pwd').value;
      var msg = '<div class="body"><form action="javascript:void(0);" method="post" name="chg_pwd" class="mainForm"><div class="rowElem"><label style="width:30%;">Current password</label><div class="formRight" style="width:65%;"><b id="curt-pwd">'+pwd+'</b></div></div><div class="rowElem"><label style="width:30%;">Password</label><div class="formRight" style="width:65%;"><input type="password" value="" name="password" id="pwd" class="validate[required]"/></div></div><div class="rowElem"><label style="width:30%;">Confirm password</label><div class="formRight" style="width:65%;"><input type="password" value="" name="confirm_password" id="c_pwd" class="validate[required,equals[pwd]]"/></div></div><div class="rowElem"><div class="formRight" style="width:65%;"><input type="submit" name="submit" value="Change password" class="greyishBtn" onclick="return validation();"/></div></div></form></div>';
      new Messi(msg, {title: 'Change password'});
      return false;
  }

  function validation()
  {
    var i = 0;
    var pwd = document.getElementById("pwd");
    var c_pwd = document.getElementById("c_pwd");
    if(pwd.value == '')
    {
        pwd.style.border='1px solid red';
        i = 1;
    }
    else pwd.style.border="";
    if(c_pwd.value == '')
    {
        c_pwd.style.border='1px solid red';
        i = 1;
    }
    else c_pwd.style.border="";
    if(c_pwd.value != pwd.value)
    {
        c_pwd.style.border='1px solid red';
        i = 1;
    }
    else c_pwd.style.border="";
    if(i == 0)
    {
        var id = $('#retailer_id').val();
        $.ajax({ url: "<?php echo site_url('retailers/update_password'); ?>",
        type: "POST",
        data: {"password":pwd.value, "c_pwd":c_pwd.value, "retailer_id":id},
        success: function(response){
        if(response)
        {
            document.getElementById("msg_msg").innerHTML = "Password for <?php echo $bean['name']; ?> was successfully updated";
            document.getElementById("note_note").style.display="block";
            document.getElementById('messi_messi').innerHTML="";
            document.getElementById('messi_messi').style.display="none";
            document.getElementById('messi_messi').setAttribute("id","donotuseme");
            document.getElementById('chk-pwd').value = pwd.value;
            window.location="#page_title";
            setTimeout(function() {
                $('#note_note').fadeOut('fast');
            }, 3000);
        }
        }});
    }
    else
        return false;
  }

 </script>
</body>
</html>
