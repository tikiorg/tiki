<?php
// Initialization
require_once('tiki-setup.php');

// Permission: needs p_register
if($allowRegister != 'y') {
  header("location: index.php");
  die;
}

$smarty->assign('showmsg','n');

if(isset($_REQUEST["register"])) {
  if($_REQUEST["pass"] <> $_REQUEST["pass2"]) {
    $smarty->assign('msg',tra("The passwords dont match"));
    $smarty->display('error.tpl');
    die;
  }
  if($userlib->user_exists($_REQUEST["name"])) {
    $smarty->assign('msg',tra("User already exists"));
    $smarty->display('error.tpl');
    die;
  }
  
  // VALIDATE NAME HERE
  if(strtolower($_REQUEST["name"])=='admin') {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display('error.tpl');
    die;
  }
  
  if(strlen($_REQUEST["name"])>37) {
    $smarty->assign('msg',tra("Username is too long"));
    $smarty->display('error.tpl');
    die;
  }
  
  if(strstr($_REQUEST["name"],' ')) {
    $smarty->assign('msg',tra("Username cannot contain whitespace"));
    $smarty->display('error.tpl');
    die; 	
  }
  
  if(!preg_match_all("/[A-Z0-9a-z_-]+/",$_REQUEST["name"],$matches)) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display('error.tpl');
    die;
  }
  
  // Check the mode
  if($useRegisterPasscode == 'y') {
    if(($_REQUEST["passcode"]!=$tikilib->get_preference("registerPasscode",md5($tikilib->genPass()))))
    {
      $smarty->assign('msg',tra("Wrong passcode you need to know the passcode to register in this site"));
      $smarty->display('error.tpl');
      die;
    }
  }
  
  if($validateUsers == 'y') {
    $apass = addslashes(substr(md5($tikilib->genPass()),0,25));
    $foo = parse_url($_SERVER["REQUEST_URI"]);
    $foo1=str_replace("tiki-register","tiki-login_validate",$foo["path"]);
    $machine ='http://'.$_SERVER["SERVER_NAME"].$foo1;
    $userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"]);
    $emails = $tikilib->get_mail_events('user_registers','*');
    foreach($emails as $email) {
      $smarty->assign('mail_user',$_REQUEST["name"]);
      $smarty->assign('mail_date',date("U"));
      $smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
      $mail_data = $smarty->fetch('mail/new_user_notification.tpl');
      mail($email, tra('New user registration'),$mail_data);
    }
    // Send the mail
    $smarty->assign('msg',tra('You will receive an email with information to login for the first time into this site'));
    $smarty->assign('mail_machine',$machine);
    $smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
    $smarty->assign('mail_user',$_REQUEST["name"]);
    $smarty->assign('mail_apass',$apass);
    $mail_data = $smarty->fetch('mail/user_validation_mail.tpl');
    mail($_REQUEST["email"], tra('Your Tiki information registration'),$mail_data);
    
    $smarty->assign('showmsg','y');
  } else {
    $userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"],'');
    header("location: index.php");
    die;
  }

}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display('tiki.tpl');
?>