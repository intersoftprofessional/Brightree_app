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
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
        <script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
        <script type="text/javascript">
          //jQuery.noConflict ();
          function close_btn(id)
          {
              var msg = 'This will permanently delete this news from the interface. Are you sure?';
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
                <div class="title">
                    <h5>News</h5>
                </div>
                <!-- Notification messages -->
                <div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
                        <p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
                </div>
                <div class="add-news-btn">
                    <div class="fr"><input type="button" value="Add news" class="greyishBtn" onclick="window.location = '<?php echo site_url('news/edit_view'); ?>';" /></div>
                </div>
                <!-- Dynamic table -->
                <div class="table">
                    <div class="head">
                        <h5 class="iFrames">Current News</h5>
                    </div>
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                        <thead>
                            <tr>
                                <th width="40%">Headline</th>
                                <th width="30%">Date published</th>
                                <th width="30%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(isset($beans))
                            {
                                $i = 0;
                                foreach ($beans as $bean)
                                {
                            ?>
                            <tr class="gradeA">
                                <td><?php echo $bean['headline']; ?></td>
                                <td><?php echo date('jS M, Y', strtotime($bean['date'])); ?></td>
                                <td class="center">
                                    <div class="num">
                                        <a class="greenNum" href="<?php echo site_url('news/edit_view/'.$bean['news_id']); ?>">
                                            <img src="<?php echo base_url(); ?>theme/sos/images/editgrey.1.png" alt="">
                                        </a><input type="hidden" id="link_del_<?php echo $i; ?>" value="<?php echo site_url('news/delete/'.$bean['news_id']); ?>">
                                        <a class="greenNum" onclick="return close_btn(<?php echo $i; ?>);" href="javascript:void(0);">
                                            <img src="<?php echo base_url(); ?>theme/sos/images/icons/dark/close.png" alt="">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                    $i++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
