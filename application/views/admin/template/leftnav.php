<?php $urls = explode('/',$_SERVER['REQUEST_URI']); ?>
            <!--<div class="logo"><a href="#" title=""><img src="<?php //echo base_url(); ?>theme/sos/images/logo.jpg" alt="" /></a></div>-->
            <div class="clear"></div>
            <div class="admin-panel">Admin Panel</div>
        <ul id="menu">
			<?php  if($this->session->userdata('user_level') == '1') { ?>
			<li class="typo"><a href="<?php echo site_url('dashboard/verify_patient_address'); ?>" title="" <?php if(in_array('dashboard',$urls)) { echo 'class="active"'; } ?>><span>Patient Module</span></a></li>
			<li class="typo"><a href="<?php echo site_url('salesorder/verify_sales_order_address'); ?>" title="" <?php if(in_array('salesorder',$urls)) { echo 'class="active"'; } ?>><span>Sales Order</span></a></li>
			<li class="typo"><a href="<?php echo site_url('counties/'); ?>" title="" <?php if(in_array('counties',$urls)) { echo 'class="active"'; } ?>><span>Counties</span></a></li>			
			<li class="typo"><a href="<?php echo site_url('taxzones'); ?>" title="" <?php if(in_array('taxzones',$urls)) { echo 'class="active"'; } ?>><span>Tax Zones</span></a></li>			
			<li class="typo"><a href="<?php echo site_url('patientidentificationlabels'); ?>" title="" <?php if(in_array('patientidentificationlabels',$urls)) { echo 'class="active"'; } ?>><span>Patient Identification Labels</span></a></li>			
			<li class="typo"><a href="<?php echo site_url('users/list_view'); ?>" title="" <?php if(in_array('users',$urls)) { echo 'class="active"'; } ?>><span>Users</span></a></li>			
			 <?php } ?>
        </ul>