<?php
// Initialization
require_once('tiki-setup.php');

if(!isset($_REQUEST["login"])) {
  header("location: $HTTP_REFERER");
  die;  
}
$isvalid = false;
if(($_REQUEST["user"] == 'admin') && (!$userlib->user_exists("admin"))) {
  if($_REQUEST["pass"]==ADMIN_PASSWORD) {
     $isvalid = true;
     $userlib->add_user("admin",ADMIN_PASSWORD,'none');
  }  
} else {
  $isvalid = $userlib->validate_user($_REQUEST["user"],$_REQUEST["pass"]);
}
if($isvalid) {
  session_register("user",$_REQUEST["user"]);
  $user = $_REQUEST["user"];
  $smarty->assign_by_ref('user',$user);
  header("location: $tikiIndex");
  die;
} else {
  $smarty->assign('msg',tra("Invalid username or password"));
  $smarty->display('error.tpl');
}
?>