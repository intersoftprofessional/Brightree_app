<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Admin Panel</title>
        <?php $this->load->view('admin/template/head_content'); ?>
		<!--<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/date-picker/tcal.css" />
		<script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/date-picker/tcal.js"></script> -->		
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
                
		<link href="<?php echo base_url(); ?>theme/sos/date-picker/jquery-ui.css" rel="Stylesheet" type="text/css" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
        
		
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
					<div class="clear"></div>
					<div class="title">
						<h5>Taxzone Module</h5>
					</div>
					
					<!-- Notification messages -->
					<div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
							<p><strong>MESSAGE: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
					</div>
					<!-- Widgets -->
					<div class="table">
						<div class="head"><h5 class="iFrames">Taxzones</h5></div>
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
							<thead>
								<tr>
									<!--<th width="15%">Retailer member number</th>-->	
                                    <th width="3%">Sr. No.</th>	
									<th width="20%">TaxZone Name</th>
									<th width="20%">TaxZone ID</th>
									<th width="20%">County</th>
									<th width="5%">Edit</th>
								</tr>
								
								<?php 
								    $html= ""; 
									$serial_no = 1;
									foreach($taxZones as $val){
									
										         $html .= '<tr>'; 
												 $html .= '<td>'.$serial_no.'</td>';
												 $html .= '<td>'.$val->taxzone_name.'</td>';
												 $html .= '<td>'.$val->taxzone_ID.'</td>';
												 $html .= '<td>'.$val->county.'</td>';
												 $html .= '<td><a href="taxzones/edit_taxzone/'.$val->ID.'">Edit</a></td>';
												 $html  .= '</tr>';
												 $html  .= '</tr>';
												 $serial_no++;
									}
									echo $html;
								?>
							</thead>
							<tbody>
							
							</tbody>
						</table>
					</div>
				</div>
			</div>
        </div>
    </body>
	<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
	
</html>