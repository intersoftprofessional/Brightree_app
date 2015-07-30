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
                    <div class="fr"><input type="button" value="Generate email" class="greyishBtn" onclick="loadPopupBox(<?php echo $live_list_id; ?>);" /></div>
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
                                                    <select name="live_list_id" id="live_list_id" class="styled">
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
                                                    <select name="list_id" id="list_id" class="styled validate[required]">
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
                <div class="fluid">
                    <div class="span6">
                       <fieldset>
                           <div class="acc first">      
                               <!-- Collapsible. Closed by default -->
                               <div class="widget">
                                   <div class="head inactive" >
                                       <h5><?php if(isset($bean) || (isset($p) && $p = 'new')) { ?>Edit offer list<?php } else {if(isset($live_list)) echo $live_list['list_name'];} ?></h5>
                                       <?php if(!isset($bean) && !isset($p)) { ?>
                                       <span class="view-btn"><input type="button" class="blueBtn first-btn" value="List view" onclick="window.location = '<?php echo site_url('offers/list_view'); ?>';" disabled/><input type="button" onclick="window.location = '<?php echo site_url('offers/order_view'); ?>';" class="blueBtn" value="Order view" /></span>
                                       <?php } ?>
                                   </div>
                                   <?php if(isset($bean) || (isset($p) && $p = 'new')) { ?>
                                   <form action="<?php echo site_url('offers/save'); ?>" method="post" class="mainForm" id="valid_1" >
                                       <input type="hidden" value="<?php if(isset($bean['list_id'])) echo $bean['list_id']; ?>" name="list_id" id="id" />
                                        <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
                                            <tbody>
                                                <tr>
                                                     <td width="30%">List name</td>
                                                     <td><input type="text" value="<?php if(isset($bean['list_name'])) echo $bean['list_name']; ?>" name="list_name" id="name" class="validate[required]" /></td>
                                                </tr>
                                                <tr>
                                                     <td width="30%">Month</td>
                                                     <td><input type="text" value="<?php if(isset($bean['month'])) echo $bean['month']; ?>" name="month" class="validate[required]"/></td>
                                                </tr>
                                                <tr>
                                                     <td width="30%">Winning number</td>
                                                     <td><input type="text" value="<?php if(isset($bean['winning_number'])) echo $bean['winning_number']; ?>" name="winning_number" id="winning_number" /></td>
                                                </tr>
                                                <?php if(!isset($bean['list_id'])) { ?>
                                                <tr>
                                                    <td width="30%">&nbsp;</td>
                                                    <td><input type="button" class="greyishBtn" value="Continue" onclick="check_list_name();" /></td>
                                                </tr>
                                                <?php }
                                                if(!isset($p))
                                                {
                                                ?>
                                                <tr>
                                                     <td width="30%"><strong>Retailers</strong></td>
                                                     <td><strong>Offers</strong></td>
                                                </tr>
                                                <?php 
                                                if(isset($retailers)) 
                                                {
                                                    $check = 1;
                                                    foreach ($retailers as $retailer)
                                                    { ?>
                                                <tr>
                                                    <td width="30%" id="checkbox745">
                                                        <input type="checkbox" value="1" name="set_homepage1[<?php echo $retailer['retailer_id']; ?>]" <?php if($offers1[$bean['list_id']][$retailer['retailer_id']]['set_homepage'] == '1') echo 'checked'; ?> id="set_homepage_<?php echo $check; ?>" onclick="add_checked(this.id);"/>
                                                        <span>
                                                            <?php echo $retailer['business_name']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php  //echo "<pre>";  print_r($offers1); exit;
                                                        if(isset($offers1[$bean['list_id']][$retailer['retailer_id']]) && $offers1[$bean['list_id']][$retailer['retailer_id']]['offer'] != '') 
                                                        {
                                                            $off = str_replace('"',"&quot;",html_entity_decode ($offers1[$bean['list_id']][$retailer['retailer_id']]['offer'])); 
                                                            $off = str_replace('<',"&lt;",$off); 
                                                            $off = str_replace('>',"&gt;",$off); 
                                                        }
                                                        else
                                                            $off = '';
                                                        ?>
                                                        <input type="text" value="<?php echo $off; ?>" name="offers1[<?php echo $retailer['retailer_id']; ?>]" />
                                                        <input type="hidden" value="<?php if(isset($offers1[$bean['list_id']][$retailer['retailer_id']]['order'])) echo $offers1[$bean['list_id']][$retailer['retailer_id']]['order']; ?>" name="order1[<?php echo $retailer['retailer_id']; ?>]" />
                                                    </td>
                                                </tr>
                                                    <?php $check++;   
                                                    }
                                                } ?>
                                                <tr>
                                                    <td width="30%">&nbsp;</td>
                                                    <td><input type="submit" class="greyishBtn" value="Save list" /></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                   </form>
                                   <?php } else { ?>
                                   <form action="<?php echo site_url('offers/save_offer_list'); ?>" method="post" class="mainForm" >
                                       <input type="hidden" value="<?php if(isset($live_list)) echo $live_list['list_id']; ?>" name="list_id" />
                                        <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
                                            <tbody>
                                                <tr>
                                                     <td width="30%">List name</td>
                                                     <td><?php echo $live_list['list_name']; ?></td>
                                                </tr>
                                                <tr>
                                                     <td width="30%">Month</td>
                                                     <td><?php echo $live_list['month']; ?></td>
                                                </tr>
                                                <tr>
                                                     <td width="30%">Winning number</td>
                                                     <td><input type="text" value="<?php echo $live_list['winning_number']; ?>" name="winning_number" id="winning_number" /></td>
                                                </tr>
                                                <tr>
                                                     <td width="30%"><strong>Retailers</strong></td>
                                                     <td><strong>Offers</strong></td>
                                                </tr>
                                                <?php 
                                                if(isset($retailers)) 
                                                {
                                                    $check = 1;
                                                    foreach ($retailers as $retailer)
                                                    { ?>
                                                <tr>
                                                    <td width="30%" id="checkbox745">
                                                        <input type="checkbox" value="1" name="set_homepage[<?php echo $retailer['retailer_id']; ?>]" <?php if($offers[$live_list['list_id']][$retailer['retailer_id']]['set_homepage'] == '1') echo 'checked'; ?> id="set_homepage_<?php echo $check; ?>" onclick="add_checked(this.id);" />
                                                        <span><?php echo $retailer['business_name']; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if(isset($offers[$live_list['list_id']][$retailer['retailer_id']])) 
                                                        {
                                                            $off = str_replace('"',"&quot;",html_entity_decode ($offers[$live_list['list_id']][$retailer['retailer_id']]['offer'])); 
                                                            $off = str_replace('<',"&lt;",$off); 
                                                            $off = str_replace('>',"&gt;",$off); 
                                                        }
                                                        else
                                                            $off = '';
                                                        ?>
                                                        <input type="text" value="<?php echo $off; ?>" name="offers[<?php echo $retailer['retailer_id']; ?>]" />
                                                        <input type="hidden" value="<?php if(isset($offers[$live_list['list_id']][$retailer['retailer_id']]['order'])) echo $offers[$live_list['list_id']][$retailer['retailer_id']]['order']; ?>" name="order[<?php echo $retailer['retailer_id']; ?>]" />
                                                    </td>
                                                </tr>
                                                    <?php
                                                    $check++;
                                                    }
                                                } ?>
                                                <tr>
                                                    <td width="30%">&nbsp;</td>
                                                    <td><input type="submit" class="greyishBtn" value="Save list" onclick="check_checkboxs()" /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                   </form>
                                   <?php } ?>
                               </div>
                               <script type="text/javascript">
                                   function add_checked(id)
                                   {
                                       if($("input[type=checkbox]:checked").length > 5)
                                       {
                                           alert("You can checked only 5 offers.");
                                           $('#'+id).attr('checked',false);
                                       }
                                   }
                               </script>
                           </div> 
                       </fieldset>   
                   </div>
                </div>
                <?php } ?>
            </div>
        </div
        </div>
    </body>
</html>
