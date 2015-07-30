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
        
<script type="text/javascript">
		function goback()
		{
			window.location = '<?php echo site_url('taxzones');?>';
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
					<div class="clear"></div>
					<div class="title">
						<h5>County Module</h5>
					</div>
					
					<!-- Notification messages -->
					<div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
							<p><strong>MESSAGE: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
					</div>
					<!-- Widgets -->
					<?php
					
						$opt ='';
						if(count($counties)>0)
						{
							foreach($counties as $county)
							{
								$sel = $result[0]->county==$county->county?'selected="selected"':'';
								$opt .='<option '.$sel.' value="'.$county->county.'">'.$county->county.'</option>';	
							}
						}
						$html = '';
						if(count($result)>0)
						{
							$i=1;
							$county='';
							foreach($result as $row )
							{
								$html .='<tr>
											<th>
												County
											</th>
											<td>
												<select name="county">
													<option value="">--Select</option>
													'.$opt.'
												</select>
												OR
												<input type="text" placeholder="Enter new county" name="county2" value="" />
												<input type="hidden" name="ID" value="'.$row->ID.'" />
											</td>
											</tr>
										<tr>
											<th>
												Taxzone ID
											</th>
											<td>
												<input type="text" name="taxzone_ID" value="'.$row->taxzone_ID.'" />
											</td>
										</tr>
										<tr>
											<th>
												Taxzone Name
											</th>
											<td>
												<input type="text" name="taxzone_name" value="'.$row->taxzone_name.'" />
											</td>
										</tr>';
								$i++;			
							}
						}
					?>
					
					<div class="table">
						<div class="head"><h5 class="iFrames">Update Taxzones</h5></div>
						<form name="frm" action="<?php echo base_url();?>index.php/taxzones/updatetaxzone" method="post">
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
							<tbody>							
								
								<?php echo $html;?>		
								<tr>
									<td colspan="3" align="right">
										<input type="submit" name="sub" value="Submit" />
										<input type="button" name="back" value="Cancel" onclick="goback();" />
									</td>
								</tr>
							</tbody>
						</table>
						</form>
					</div>
				</div>
			</div>
        </div>
    </body>
	<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>	
</html>