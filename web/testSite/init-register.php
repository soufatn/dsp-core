<?php
require_once("./include/Membership.php");

$fgmembersite = new Membership();
if (isset($_POST['submitted']))
{
    if ($fgmembersite->initAdmin())
    {
        $state = $fgmembersite->getState();
        switch ($state) {
        case 'init required':
            $fgmembersite->redirectToURL("init-system.php");
            break;
        case 'admin required':
            $fgmembersite->redirectToURL("init-register.php");
            break;
        }
        $fgmembersite->redirectToURL("thank-you-system.html");
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <title>Cloud Initialization - Admin Setup</title>
    <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
    <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
    <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
    <script src="scripts/pwdwidget.js" type="text/javascript"></script>
</head>
<body>

<!-- Form Code Start -->
<div id='fg_membersite'>
<form id='register' action='<?php echo $fgmembersite->getSelfScript(); ?>' method='post' accept-charset='UTF-8'>
<fieldset >
<legend>Administrator Registration</legend>

<input type='hidden' name='submitted' id='submitted' value='1'/>

<div class='short_explanation'>* required fields</div>
<input type='text'  class='spmhidip' name='<?php echo $fgmembersite->getSpamTrapInputName(); ?>' />

<div><span class='error'><?php echo $fgmembersite->getErrorMessage(); ?></span></div>
<div class='container'>
    <label for='name' >First Name:</label><br/>
    <input type='text' name='firstname' id='firstname' value='<?php echo $fgmembersite->safeDisplay('firstname') ?>' maxlength="50" /><br/>
    <span id='register_name_errorloc' class='error'></span>
</div>
    <div class='container'>
        <label for='name' >Last Name:</label><br/>
        <input type='text' name='lastname' id='lastname' value='<?php echo $fgmembersite->safeDisplay('lastname') ?>' maxlength="50" /><br/>
        <span id='register_name_errorloc' class='error'></span>
    </div>
<div class='container'>
    <label for='email' >Email Address*:</label><br/>
    <input type='text' name='email' id='email' value='<?php echo $fgmembersite->safeDisplay('email') ?>' maxlength="50" /><br/>
    <span id='register_email_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='username' >UserName*:</label><br/>
    <input type='text' name='username' id='username' value='<?php echo $fgmembersite->safeDisplay('username') ?>' maxlength="50" /><br/>
    <span id='register_username_errorloc' class='error'></span>
</div>
<div class='container' style='height:80px;'>
    <label for='password' >Password*:</label><br/>
    <div class='pwdwidgetdiv' id='thepwddiv' ></div>
    <noscript>
    <input type='password' name='password' id='password' maxlength="50" />
    </noscript>    
    <div id='register_password_errorloc' class='error' style='clear:both'></div>
</div>
<input type="hidden" name="admin" value="true">

<div class='container'>
    <input type='submit' name='Submit' value='Submit' />
</div>

</fieldset>
</form>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

<script type='text/javascript'>
// <![CDATA[
    var pwdwidget = new PasswordWidget('thepwddiv','password');
    pwdwidget.MakePWDWidget();
    
    var frmvalidator  = new Validator("register");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();
    frmvalidator.addValidation("firstname","req","Please provide your first name");
    frmvalidator.addValidation("lastname","req","Please provide your last name");

    frmvalidator.addValidation("email","req","Please provide your email address");

    frmvalidator.addValidation("email","email","Please provide a valid email address");

    frmvalidator.addValidation("username","req","Please provide a username");
    
    frmvalidator.addValidation("password","req","Please provide a password");

// ]]>
</script>

<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>