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

    <body style="cursor: auto;">
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
                <div class="sample-video"><a href="http://www.soldonstourport.co.uk/vids/retailers/retailers.swf" rel="shadowbox;width=1200;height=760"><img src="<?php echo base_url(); ?>theme/sos/images/watch-this.png" /></a></div>
                <div class="clear"></div>
                <div class="title">
                    <h5>Offers</h5>
                </div>
                <?php
                    foreach($lists as $list)
                    { 
                        if($list['status'] == '1')
                            $live_list_id = $list['list_id'];
                        else
                            $live_list_id = '0';
                    }
                ?>
                <!-- Notification messages -->
                <div class="nNote nSuccess hideit" <?php if(!isset($msg)) echo "style='display:none;'"; ?> id="note_note">
                        <p><strong>SUCCESS: </strong><span id="msg_msg"><?php if(isset($msg)) echo $msg; ?></span></p>
                </div>
                <div class="add-news-btn">
                    <div class="fr"><input type="button" value="Generate email" class="greyishBtn" onclick="loadPopupBox(<?php echo $live_list_id; ?>);"/></div>
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
                                            Current live list
                                        </h5>
                                    </div>
                                    <div class="body">
                                        <form name="add_user" action="<?php echo site_url('offers/change_live_list'); ?>" method="post" class="mainForm" >
                                            <div class="rowElem">
                                                <div class="live-formRight">
                                                    <?php $name = '';
                                                        if(isset($lists))
                                                        {
                                                            foreach($lists as $list)
                                                            { 
                                                                if($list['status'] == '1') $name = $list['list_name'];
                                                            }
                                                        }
                                                    ?>
                                                    <div id="uniform-live_list_id" class="selector">
                                                    <span style="-moz-user-select: none;"><?php if($name != '') echo $name; else echo 'Select'; ?></span>
                                                    <select name="live_list_id" id="live_list_id" class="styled" onchange="$('#uniform-live_list_id span').html($('#'+this.id+' option:selected').text());">
                                                        <option value="">Select</option>
                                                        <?php
                                                        if(isset($lists))
                                                        {
                                                            foreach($lists as $list)
                                                            { ?>
                                                        <option value="<?php echo $list['list_id']; ?>" <?php if($list['status'] == '1') echo 'selected'; ?>><?php echo $list['list_name']; ?></option>
                                                            <?php    
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="live-formRight">
                                                    <input type="submit" class="greyishBtn" value="Change" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
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
                                            Add/Edit Offers
                                        </h5>
                                    </div>
                                    <div class="body">
                                        <form name="add_user" action="<?php echo site_url('offers/list_view'); ?>" method="post" class="mainForm" id="valid_2" >
                                            <input type="hidden" value="edit" name="action" />
                                            <div class="rowElem">
                                                <label>Current lists</label>
                                                <div class="formRight">
                                                    <?php
                                                        $name1 = '';
                                                        if(isset($lists))
                                                        {
                                                            foreach($lists as $list)
                                                            {
                                                                if(isset($bean['list_id']) && $list['list_id'] == $bean['list_id']) 
                                                                    $name1 = $list['list_name'];    
                                                            }
                                                        }
                                                    ?>
                                                    <div id="uniform-list_id" class="selector">
                                                        <span style="-moz-user-select: none;"><?php if($name1 != '') echo $name1; else echo 'Select'; ?></span>
                                                            <select name="list_id" id="list_id" class="styled validate[required]" onchange="$('#uniform-list_id span').html($('#'+this.id+' option:selected').text());">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if(isset($lists))
                                                                {
                                                                    foreach($lists as $list)
                                                                    { ?>
                                                                <option value="<?php echo $list['list_id']; ?>"  <?php if(isset($bean['list_id']) && $list['list_id'] == $bean['list_id']) echo 'selected'; ?>><?php echo $list['list_name']; ?></option>
                                                                    <?php    
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rowElem">
                                                <label></label>
                                                <div class="formRight">
                                                    <input type="submit" class="greyishBtn" value="Edit" style="margin-right:20px;"/><input type="button" class="greyishBtn" value="Create new list" onclick="$('#create_new').submit();" /></form>
                                                </div>
                                            </div>
                                        </form>
                                        <form name="add_user" action="<?php echo site_url('offers/list_view'); ?>" method="post" class="mainForm" id="create_new" ><input type="hidden" value="new" name="p" /></form>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <?php if(isset($bean) || (isset($p) && $p == 'new') || isset($live_list)) { ?>
                <!-- Notification messages -->
                <div class="nNote nSuccess hideit" style='display:none; width: 596px;' id="response">
                        
                </div>
                <div class="fluid">
                    <div class="span6">
                       <fieldset>
                           <div class="acc first" id="order_view">      
                               <!-- Collapsible. Closed by default -->
                               <div class="widget">
                                   <div class="head inactive" >
                                       <h5><?php if(isset($bean) || (isset($p) && $p = 'new')) { ?>Edit offer list<?php } else {if(isset($live_list)) echo $live_list['list_name'];} ?></h5>
                                       <?php if(!isset($bean) && !isset($p)) { ?>
                                       <span class="view-btn">
                                           <input type="button" class="blueBtn first-btn" value="List view" onclick="window.location = '<?php echo site_url('offers/list_view'); ?>';"/>
                                           <input type="button" class="blueBtn" value="Order view" onclick="window.location = '<?php echo site_url('offers/order_view'); ?>';" disabled/>
                                       </span>
                                       <?php } ?>
                                   </div>
                                   <div id="list">
                                        <input type="hidden" value="<?php if(isset($live_list)) echo $live_list['list_id']; ?>" name="list_id" id="live-live-list_id" />
                                            <?php 
                                                if(isset($retailers)) 
                                                {
                                                    foreach ($retailers as $retailer)
                                                    { 
                                                        if($offers[$live_list['list_id']][$retailer['retailer_id']]['offer'] != '')
                                                        {
                                                            $offer[$offers[$live_list['list_id']][$retailer['retailer_id']]['order']]['id'] = $retailer['retailer_id'];
                                                            $offer[$offers[$live_list['list_id']][$retailer['retailer_id']]['order']]['business_name'] = $retailer['business_name'];
                                                            $offer[$offers[$live_list['list_id']][$retailer['retailer_id']]['order']]['offer'] = $offers[$live_list['list_id']][$retailer['retailer_id']]['offer'];
                                                        }
                                                    }
                                                }
                                                ksort($offer); 
                                                //print_r($offer); 
                                            ?>
                                            <ul>
                                                <?php
                                                foreach($offer as $off)
                                                {
                                                    ?>
                                                <li id="arrayorder_<?php echo $off['id']; ?>">
                                                    <span class="first"><?php echo $off['business_name']; ?></span>
                                                    <span class="second"><?php if(isset($off['offer'])) echo html_entity_decode($off['offer']); ?></span>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                   </div>
                               </div>
                           </div> 
                       </fieldset>   
                   </div>
                </div>
                <?php } ?>
            </div>
        </div
        </div>
        <script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/jquery/1.3.2/jquery.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>theme/sos/jquery/1.7.2/jquery-ui.js"></script>
        <!--For drag and drop list-->
        <script type="text/javascript">
        
        $(document).ready(function(){ 	
                  function slideout(){
          setTimeout(function(){
          $("#response").slideUp("slow", function () {
              });

        }, 2000);}

            $("#response").hide();
                $(function() {
                $("#list ul").sortable({ opacity: 0.8, cursor: 'move', update: function() {

                                var order = $(this).sortable("serialize") + '&update=update&list_id='+$('#live-live-list_id').val(); 
                                $.post("<?php echo site_url('offers/update_order'); ?>", order, function(theResponse){
                                        $("#response").html(theResponse);
                                        $("#response").slideDown('slow');
                                        slideout();
                                }); 															 
                        }								  
                        });
                });

        });	
        </script>
    </body>
</html>
