<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>Admin Panel</title>
<?php $this->load->view('admin/template/head_content');  ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/sos/messi/messi.css" />
<script src="<?php echo base_url(); ?>theme/sos/messi/messi.js"></script>
<script type="text/javascript">
    //jQuery.noConflict ();
    function forgot_pwd()
    {
        var msg = '<div class="body" id="forgot-pwd-msg"><form action="javascript:void(0);" method="post" name="forgot_pwd" class="mainForm"><div class="rowElem"><label style="width:50%;">Please enter your email address</label><div class="formRight" style="width:46%;"><input type="text" value="" name="email_id" id="email_id" class="validate[required]"/></div></div><div class="rowElem"><div class="formRight"  style="width:46%;"><input type="submit" name="submit" value="Get password" class="greyishBtn" onclick="return validation();"/></div></div></form></div>';
        new Messi(msg, {title: 'Forgotten password?'});
        return false;
    }
    
  function validation()
  {
    var i = 0;
    var email = document.getElementById("email_id");
    if(email.value == '')
    {
        email.style.border='1px solid red';
        i = 1; 
    }
    else email.style.border="";
    if(i == 0)
    {
        $.ajax({ url: "<?php echo site_url('admin/forgot_password'); ?>", 
        type: "POST",
        data: {"email":email.value},
        success: function(response){
        if(response)
        {
            document.getElementById("forgot-pwd-msg").style.padding = '10px';
            if(response == 'true')
            {
                document.getElementById("forgot-pwd-msg").innerHTML = "Your password has been sent to your email address.";
                document.getElementById("forgot-pwd-msg").style.border = '1px solid green';
            }
            else
            {
                document.getElementById("forgot-pwd-msg").innerHTML = "That email address isn't present in our database.";
                document.getElementById("forgot-pwd-msg").style.border = '1px solid red';
            }
        }
        }});
    }
    else
        return false;
  }
</script>
</head>

<body>

<!-- Login form area -->
<div class="loginWrapper">
    <!--<div class="loginLogo"><img src="<?php //echo base_url(); ?>theme/sos/images/sos-logo.png" alt="" /></div>-->
    <div class="loginPanel">
        <div class="head"><h5 class="iUser">Login</h5></div>
        <form action="<?php echo site_url('admin/login'); ?>" id="valid" class="mainForm" method="post">
            <fieldset>
                <div class="loginRow noborder">
                    <label for="req1">Email:</label>
                    <div class="loginInput"><input type="text" name="email" class="validate[required]" id="req1" value="<?php echo $this->input->cookie('email'); ?>" /></div>
                </div>
                
                <div class="loginRow">
                    <label for="req2">Password:</label>
                    <div class="loginInput"><input type="password" name="password" class="validate[required]" id="req2" value="<?php echo $this->input->cookie('password'); ?>" /></div>
                </div>
                
                <div class="loginRow">
                    <div class="rememberMe"><input type="checkbox" id="check2" name="remember_me" value="check" <?php if($this->input->cookie('email') != '') echo 'checked'; ?> /><label for="check2">Remember me</label></div>
                    <div class="submitForm"><input type="submit" value="Log me in" class="blueBtn" /></div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="loginRow"><a href="javascript:void(0);" onclick="forgot_pwd();">forgotten password?</div>
</div>

<!-- Footer -->
<div id="footer">
	<div class="wrapper">

    </div>
</div>

</body>
</html>
<?php exit; ?>