<?php
// Initialization
require_once('tiki-setup.php');

$isvalid = false;
$isvalid = $userlib->validate_user($_REQUEST["user"],$_REQUEST["pass"],'','');
if($isvalid) {
  session_register("user",$_REQUEST["user"]);
  $user = $_REQUEST["user"];
  $smarty->assign_by_ref('user',$user);
  //Now since the user is valid we put the user provpassword as the password 
  $userlib->confirm_user($user);
  header("location: $tikiIndex");
  die;
} else {
  $smarty->assign('msg',tra("Invalid username or password"));
  $smarty->display('error.tpl');
}
?>