<?php
// Initialization
require_once('tiki-setup.php');

// Permission: needs p_register
if($allowRegister != 'y') {
  header("location: index.php");
  die;
}

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
  $userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"]);
  header("location: index.php");
  die;
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display('tiki.tpl');
?>