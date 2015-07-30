<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>SOS : Admin Panel</title>
        <script src="<?php echo base_url(); ?>theme/sos/drop/jquery.js"></script>
        <script src="<?php echo base_url(); ?>theme/sos/drop/javascript.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/date-picker/tcal.css" />
        <script src="<?php echo base_url(); ?>theme/sos/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/date-picker/tcal.js"></script> 
        <?php $this->load->view('admin/template/head_content'); ?>
        <script type="text/javascript">
            setTimeout(function() {
                $('#note_note').fadeOut('fast');
            }, 3000);
            
            function delete_file(id,path, file_name)
            {
                $.ajax({ url: "<?php echo site_url('news/delete_uploaded_file'); ?>", 
                            type: "POST",
                            data: { "id":id,"path":path,"file_name":file_name},
                            success: function(response){
                            if(response)
                                document.getElementById(file_name).innerHTML = '';
                    }});
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
                <!-- Widgets -->
                <div class="fluid">
                    <div class="span6">
                        <fieldset>
                            <div class="widget acc first">      
                                <!-- Collapsible. Closed by default -->
                                <div class="widget">
                                    <div class="head inactive">
                                        <h5>
                                            Add/Edit News
                                        </h5>
                                    </div>
                                    <div class="body">
                                        <form name="add_user" action="<?php echo site_url('news/save'); ?>" method="post" class="mainForm" id="valid" enctype="multipart/form-data">
                                            <input type="hidden" value="<?php if(isset($bean['news_id'])) echo $bean['news_id']; ?>" name="news_id"/>
                                            <div class="rowElem">
                                                <label>Headline</label>
                                                <div class="formRight">
                                                    <input type="text" value="<?php if(isset($bean['headline'])) echo $bean['headline']; ?>" name="headline" id="headline" class="validate[required]" />
                                                </div>
                                            </div>
                                            <div class="rowElem">
                                                <label>Content</label>
                                                <div class="formRight">
                                                    <textarea name="content" id="content"><?php if(isset($bean['content'])) echo $bean['content']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="rowElem">
                                                <label>Image</label>
                                                <div class="formRight fileuploader" id="drop-files" ondragover="return false">
                                                    <input type="file" value="" id="file" name="file" />
                                                    <?php if(isset($bean['image']) && $bean['image'] != '0') $imgs = explode(',', $bean['image']); ?>
                                                    <ul id="dropped-files" class="dropped-sign">
                                                        <?php
                                                        if(isset($imgs) && !empty($imgs))
                                                        {
                                                            foreach($imgs as $id=>$value)
                                                            {
                                                                $file_name = explode('/', $value);
                                                                ?>
                                                        <li id="<?php echo $file_name[2]; ?>">
                                                            <input type="hidden" value="<?php echo $value; ?>" name="image[]"/>
                                                            <a href="<?php echo base_url(); ?>theme/sos/drop/<?php echo $value; ?>" target="_blank">
                                                                <?php echo $file_name[2]; ?>
                                                            </a>
                                                            <span onclick="delete_file(<?php echo $bean['news_id']; ?>,'<?php echo $value; ?>','<?php echo $file_name[2]; ?>')">
                                                                <img src="<?php echo base_url(); ?>theme/sos/images/delete-btn.png" alt="" />
                                                            </span>
                                                        </li>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    <div id="loading_img" style="display:none;">
                                                        <img src="<?php echo base_url(); ?>theme/rs-admin/images/loading.gif" alt=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rowElem">
                                                <label>Published date</label>
                                                <div class="formRight" id="published-date-div">
                                                    <input type="text" value="<?php if(isset($bean['date'])) echo $bean['date']; ?>" name="date" id="date" class="validate[required] tcal" />
                                                    <aside id="cal_cal"></aside>
                                                </div>
                                            </div>
                                            <div class="rowElem">
                                                <label></label>
                                                <div class="formRight">
                                                    <input type="submit" class="greyishBtn" value="Add/Edit" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
