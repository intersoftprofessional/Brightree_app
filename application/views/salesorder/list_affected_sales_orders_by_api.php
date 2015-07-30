<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Admin Panel</title>
        <?php $this->load->view('admin/template/head_content'); ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/date-picker/tcal.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/date-picker/tcal.js"></script> 
		<script type="text/javascript">
            setTimeout(function() {
                $('#note_note').fadeOut('fast');
            }, 7000);
            
            function loadPopupBox(id)
            {
                if(id != '0')
                {
                    $.ajax({ url: "<?php echo site_url('offers/generate_email'); ?>", 
                        type: "POST",
                        data: { "list_id":id },
                        success: function(response){
                            $('#email-code').html(response);
                    }});

                    $('#create-new-list').fadeIn("slow");
                    $("#container").css({ // this is just for style		
                                                "opacity": "0.3"  
                                        }); 
                }
                else
                    alert('Please select live list offer.');
            }

            // When site loaded, load the Popupbox First
            function unloadPopupBox(id) 
            {	// To Load the Popupbox
                if(id == '0')
                    var pre = '';
                else
                    var pre = '-'+id;
                $('#create-new-list'+pre).fadeOut("slow");
                $("#container").css({ // this is just for style		
                                            "opacity": "1"  
                                    });                                     
            }
            function check_list_name()
            {
                var list_name = $('#name').val();
                var id = $('#id').val();
                if(list_name != '')
                {
                    $.ajax({ url: "<?php echo site_url('offers/check_list_name'); ?>", 
                        type: "POST",
                        data: { "list_id":id,"list_name":list_name },
                        success: function(response){
                        if(response== "true")
                        { 
                           $('#valid_1').submit();
                        }
                        else
                        {
                            alert('name already used.');
                        }
                    }});
                }
                else
                    $('#valid_1').submit();
            }
        </script>
    </head>

    <body>
        
        <div id="create-new-list" style="display:none;">
            <a class="popupBoxClose" href="javascript:void(0);" onclick="unloadPopupBox(0);">x</a>
            <div>
                <h5>Source code</h5>
                <div id="email-code"></div>
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
			<div class="wrapper">
				<!-- Left navigation -->
				<div class="leftNav">
					<?php $this->load->view('admin/template/leftnav');?>
				</div>
				
				<!-- Content -->
				<div class="content" id="page_title">
				<!--<div class="sample-video"><a href="http://www.soldonstourport.co.uk/vids/retailers/retailers.swf" rel="shadowbox;width=1200;height=760"><img src="<?php // echo base_url(); ?>theme/sos/images/watch-this.png" /></a></div>-->
					<div class="clear"></div>
					<div class="title">
						<h5>Sales Order Module</h5>
					</div>
					
					<!-- Notification messages -->
					<div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
							<p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
					</div>
					<!--<div class="add-news-btn">
						<div class="fr"><input type="button" value="Generate email" class="greyishBtn" onclick="loadPopupBox(<?php echo $live_list_id; ?>);" /></div>
					</div>-->	
					<a style="position: relative; top: 20px;" href="<?php echo site_url('salesorder/verify_sales_order_address'); ?>"><img alt="" src="<?php echo base_url(); ?>/theme/sos/images/back_arrow.jpg"><span>Back to Sales Order Address Requests list</span></a>
					<div class="table">
						<div class="head"><h5 class="iFrames">Effected Sales Orders List</h5></div>
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
							<thead>
								<tr>
									<!--<th width="15%">Retailer member number</th>-->									
									<th width="10%">Sales Order's Brightree Id</th>
									<th width="8%">Patient Name</th>									
									<th width="5%">Address Updated</th>
									<th width="25%">Previous Address</th>
									<th width="25%">Updated Address</th>
									<th width="14%">Failure Message</th>
									<th width="25%">Operations</th>
								</tr>
							</thead>
							<tbody>
							<?php $li = 0; foreach($beans as $bean) { ?>
								<tr class="gradeA">
									<!--<td><?php //echo $bean['retailer_member_number']; ?></td>-->									
									<td><?php echo $bean['sales_order_id']; ?></td>
									<td><?php echo $bean['patient_name']; ?></td>									
									<td><?php echo ($bean['address_updated']) ? 'Yes' : 'No'; ?></td>
									<td><?php echo $bean['old_address']; ?></td>
									<td><?php echo $bean['new_address']; ?></td>
									<td><?php echo $bean['failure_message']; ?></td>
									<td class="center"><div class="num">
										<!--<a href="<?php //echo site_url('dashboard/list_affected_patients_by_api/'.$bean['ID']); ?>" class="greenNum">
										<img alt="" src="<?php //echo base_url(); ?>theme/sos/images/view_icon.png">
										</a>-->
										<input type="hidden" value="<?php echo site_url('dashboard/delete_affected_sales_order/'.$bean['ID'].'/'.$bean['sales_order_api_call_id']); ?>" id="link_del_<?php echo $li; ?>"/>
										<a href="javascript:void(0);" title="Delete" onclick="return close_btn1(<?php echo $li; ?>);" class="greenNum">
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
			</div>
        </div>
    </body>
	<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
	<script type="text/javascript">
		//jQuery.noConflict ();
		function close_btn1(id)
		{
			var msg = 'This will permanently delete this record. Are you sure?';
			new Messi(msg, {title: 'Alert', buttons: [{id: 0, label: 'Yes', val: 'Y'}, {id: 1, label: 'No', val: 'N'}], callback: function(val) {
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
</html>