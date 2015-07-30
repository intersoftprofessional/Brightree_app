<?php $urls = explode('/',$_SERVER['REQUEST_URI']); ?>
            <!--<div class="logo"><a href="#" title=""><img src="<?php //echo base_url(); ?>theme/sos/images/logo.jpg" alt="" /></a></div>-->
            <div class="clear"></div>
            <div class="admin-panel">Admin Panel</div>
        <ul id="menu">
            <?php /* if($this->session->userdata('user_level') == '1') { ?>
            <!--<li class="typo"><a href="<?php echo site_url('retailers/list_view'); ?>" title="" <?php if(in_array('retailers',$urls)) { echo 'class="active"'; } ?>><span>Retailers</span></a></li>
            <li class="typo"><a href="<?php echo site_url('news/list_view'); ?>" title="" <?php if(in_array('news',$urls)) { echo 'class="active"'; } ?>><span>News</span></a></li>
            <li class="typo"><a href="<?php echo site_url('offers/list_view'); ?>" title="" <?php if(in_array('offers',$urls)) { echo 'class="active"'; } ?>><span>Offers</span></a></li>
            <?php } ?>
            <?php if($this->session->userdata('user_level') == '0') { ?>
            <li class="typo"><a href="<?php echo site_url('retailers/profile'); ?>" title="" <?php if(in_array('profile',$urls)) { echo 'class="active"'; } ?>><span>Your profile</span></a></li>
            <li class="typo"><a href="<?php echo site_url('retailers/page_edit_view'); ?>" title="" <?php if(in_array('page_edit_view',$urls)) { echo 'class="active"'; } ?>><span>Edit your page</span></a></li>
            <li class="typo"><a href="<?php echo site_url('retailers/social_media'); ?>" title="" <?php if(in_array('social_media',$urls)) { echo 'class="active"'; } ?>><span>Social media feeds</span></a></li>
            <li class="typo"><a href="<?php echo site_url('retailers/upload_profile_photo'); ?>" title="" <?php if(in_array('upload_profile_photo',$urls)) { echo 'class="active"'; } ?>><span>Upload main profile photo</span></a></li>
            <!--<li class="typo"><a href="<?php echo site_url('retailers/upload_photos'); ?>" title="" <?php if(in_array('upload_photos',$urls)) { echo 'class="active"'; } ?>><span>Upload additional photos</span></a></li>-->
            <?php } */?>
			
			<?php  if($this->session->userdata('user_level') == '1') { ?>
			<li class="typo"><a href="<?php echo site_url('dashboard/verify_patient_address'); ?>" title="" <?php if(in_array('dashboard',$urls)) { echo 'class="active"'; } ?>><span>Patient Module</span></a></li>
			<li class="typo"><a href="<?php echo site_url('salesorder/verify_sales_order_address'); ?>" title="" <?php if(in_array('salesorder',$urls)) { echo 'class="active"'; } ?>><span>Sales Order</span></a></li>
			<li class="typo"><a href="<?php echo site_url('counties/'); ?>" title="" <?php if(in_array('counties',$urls)) { echo 'class="active"'; } ?>><span>Counties</span></a></li>			
			<li class="typo"><a href="<?php echo site_url('taxzones'); ?>" title="" <?php if(in_array('taxzones',$urls)) { echo 'class="active"'; } ?>><span>Tax Zones</span></a></li>			
			<li class="typo"><a href="<?php echo site_url('users/list_view'); ?>" title="" <?php if(in_array('users',$urls)) { echo 'class="active"'; } ?>><span>Users</span></a></li>			
			 <?php } ?>
        </ul>