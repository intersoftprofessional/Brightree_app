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
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
</head>

<body onload="charcount();">

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
    	<div class="title"><h5>Patient Identification Labels</h5></div>
        <!-- Notification messages -->
        <div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
                <p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
        </div>
		<a class="btnIconLeft" href="<?php echo site_url('patientidentificationlabels/fetch_sales_order_ready_for_shipping'); ?>" style="position: relative; top: 20px;">
			<img class="icon" alt="" src="<?php echo base_url(); ?>/theme/sos/images/refresh.png">
			<span>Click to fetch latest sales orders from Brightree those are ready for shipping</span>
		</a>
        <!-- Dynamic table -->
        <div class="table">
            <div class="head"><h5 class="iFrames">Sales Orders Ready For Shipping</h5></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                <thead>
                    <tr>
                        <!--<th width="15%">Retailer member number</th>-->
                        <th width="4%">Sr.</th>	
						<th width="10%">Sales Order ID</th>
						<th width="10%">Patient Name</th>
						<th width="10%">WIP Assigned To Person</th>
						<th width="10%">WIP Days In State</th>
						<th width="10%">WIP Need Date</th>
						<th width="10%">Facility</th>
						<th width="10%">WIP Status</th>
						<th width="8%">Shipped</th>
						<th width="10%">Operations</th>
                    </tr>
                </thead>
                <tbody>
                <?php $li = 1; foreach($salesorders as $val) { ?>
                    <tr class="gradeA">
                        <!--<td><?php //echo $bean['retailer_member_number']; ?></td>-->
                        <td><?php echo $li; ?></td>
                        <td><?php echo $val->sales_order_id; ?></td>
                        <td><?php echo $val->patient_name; ?></td>
                        <td><?php echo $val->WIPAssignedToPerson; ?></td>
                        <td><?php echo $val->WIPDaysInState; ?></td>
                        <td><?php echo $val->WIPNeedDate; ?></td>
                        <td><?php echo $val->facility; ?></td>
						<td><?php echo $val->WIPStateName; ?></td>
						<td><?php echo ($val->shipped == 1) ? 'Shipped' : '' ; ?></td>
                        <td><input type="hidden" value="<?php echo site_url("patientidentificationlabels/delete_salesorder/$val->ID"); ?>" id="link_del_<?php echo $li; ?>"/><?php 
						echo '<a class="btnIconLeft" href="javascript:void(0);" onclick="return printLabels('.$li.');"><img class="icon" src="'.base_url().'theme/sos/images/printer.png" alt="print"><span style="min-width:55px">Print</span></a>';
						echo '<a class="btnIconLeft" href="'.site_url("patientidentificationlabels/labels/$val->ID").'"><img class="icon" src="'.base_url().'theme/sos/images/icon_boards.gif" alt="print"><span style="min-width:55px;">FullFill</span></a>';
						echo '<a class="btnIconLeft" href="javascript:void(0);" onclick="return close_btn1('.$li.');"><img class="icon" src="'.base_url().'theme/sos/images/icons/dark/close.png" alt="print"><span style="min-width:55px;">Delete</span></a>';
						?></td>

                    </tr>
					<!-- Start Add Labels To Be Print On Click On Print Buttom -->
					<?php if(count($val->labels) > 0) { ?>
						<div style="display:none;" id="labels-<?php echo $li;?>">						
							<?php 
							foreach($val->labels as $label) { ?>								
									<div class="cont">
										<div class="label clearfix">
											<div class="clearfix">
												<div class="left-cont">
													<div class="part-1 clearfix">
														<div class="ship-label" style="">Facility :</div>
														<div class="code"><?php echo strtolower($val->facility); ?></div>
													</div>
													<div class="part-1 clearfix">
														<div class="ship-label">Patient name :</div>
														<div class="code"><?php echo strtolower($val->patient_name); ?></div>
													</div>
													<div class="part-1 clearfix">
														<div class="ship-label">Room #</div>
														<div class="code">&nbsp;</div>
													</div>
													<div class="part-1 clearfix">
														<div class="ship-label">Sales order#</div>
														<div class="code"><?php echo $val->sales_order_id; ?></div>
													</div>
												</div>
												<div class="right-cont">												
													<img src="<?php echo site_url('patientidentificationlabels/generate_barcode_image/'.$label->barcode); ?>" alt="" />
													<div style="text-align:center; font-size:12px;line-height: 5px;letter-spacing:3px"><?php echo $label->barcode; ?></div>
													<div class="ship-label" style="font-size:13px; margin:5px 0;letter-spacing:.5px;font-weight:600;"><?php echo $label->ID; ?></div>
												</div>
											</div>
										</div>
									</div>
								<?php if(end($val->labels) !== $label) {?>
									<div class="page-break"></div>
								<?php } ?>
							<?php } ?>
						</div>
					<?php } ?>	
					<!-- End Add Labels To Be Print On Click On Print Buttom -->
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
<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
<script type="text/javascript">		
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
		function printLabels(labelcount){
			if(document.getElementById('frame1')) {
				document.body.removeChild(document.getElementById('frame1'));
			}
			
			var frame1 = document.createElement('iframe');
			frame1.id = "frame1";
			frame1.name = "frame1";
			frame1.style.position = "absolute";
			frame1.style.top = "-1000000px";
			//frame1.style.display = "none";
			document.body.appendChild(frame1);
			var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
			
			var link = frameDoc.document.createElement('link');
			link.type = 'text/css';
			link.rel = 'stylesheet';
			link.href = '<?php echo base_url('theme/sos/css/printstyle.css'); ?>';
			
			var link1 = frameDoc.document.createElement('link');
			link1.type = 'text/css';
			link1.rel = 'stylesheet';
			link1.href = 'https://fonts.googleapis.com/css?family=Open+Sans:400,600';
			
			
			
			frameDoc.document.open();    
			//Add Content To Print
			frameDoc.document.write(document.getElementById("labels-"+labelcount).innerHTML);			
			frameDoc.document.body.appendChild(link);			
			frameDoc.document.body.appendChild(link1);			
			frameDoc.document.close();
			
			//setTimeout(function() {
			window.frames["frame1"].focus();
			window.frames["frame1"].print();
				//document.body.removeChild(frame1);
			//}, 500);
		}
</script>		
<script src="<?php echo base_url(); ?>theme/sos/upload_script/js/script.js"></script>
</body>
</html>