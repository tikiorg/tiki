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
  
  if(!preg_match_all("/[A-Z0-9a-z_-]+/",$_REQUEST["name"],$matches)) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display('error.tpl');
    die;
  }
  
  // Check the mode
  if($validateUsers == 'y') {
    $apass = addslashes(substr(md5($tikilib->genPass()),0,25));
    $foo = parse_url($_SERVER["REQUEST_URI"]);
    $foo1=str_replace("tiki-register","tiki-login_validate",$foo["path"]);
    $machine ='http://'.$_SERVER["SERVER_NAME"].$foo1;
    $message = tra('Hi')." ".$_REQUEST["name"]." ".tra('you are about to be a registered user in')." ".$_SERVER["SERVER_NAME"]." \n";
    $message .= tra('use this link to login for the first time')."\n";
    $message .=$machine."?user=".$_REQUEST["name"]."&pass=".$apass;
    $userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"]);
    // Send the mail
    @mail($_REQUEST["email"], tra('your registration information for')." ".$_SERVER["SERVER_NAME"],$message);
    $smarty->assign('msg',tra('You will receive an email with information to login for the first time into this site'));
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