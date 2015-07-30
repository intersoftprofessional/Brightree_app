<div class="fixed">
        <div class="wrapper">
            <div class="welcome"><a href="#" title=""><img src="<?php echo base_url(); ?>theme/sos/images/userPic.png" alt="" /></a><span>Hi <?php if($this->session->userdata('user_level') == '1') echo 'Administrator'; else echo $this->session->userdata('name'); ?></span></div>
            <div class="userNav">
                <ul>
                    <!--<li><a href="#" title=""><img src="<?php echo base_url(); ?>theme/sos/images/icons/topnav/profile.png" alt="" /><span>Profile</span></a></li>-->
                    <li><a href="<?php echo site_url('admin/logout'); ?>" title=""><img src="<?php echo base_url(); ?>theme/sos/images/icons/topnav/logout.png" alt="" /><span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>
