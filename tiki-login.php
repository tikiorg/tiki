<?php
// Initialization
require_once('tiki-setup.php');

/*
if(!isset($_REQUEST["login"])) {
  header("location: $HTTP_REFERER");
  die;  
}
*/
$isvalid = false;
$isdue=false;
if(($_REQUEST["user"] == 'admin') && (!$userlib->user_exists("admin"))) {
  if($_REQUEST["pass"]==ADMIN_PASSWORD) {
     $isvalid = true;
     $userlib->add_user("admin",ADMIN_PASSWORD,'none');
  }  
} else {
  if(!isset($_REQUEST["challenge"])) $_REQUEST["challenge"]='';
  $isvalid = $userlib->validate_user($_REQUEST["user"],$_REQUEST["pass"],$_REQUEST["challenge"]);
  // If the password is valid but it is due then force the user to change the password by
  // sending the user to the new password change screen without letting him use tiki
  // The user must re-nter the old password so no secutiry risk here
}

if($isdue) {

}

if($isvalid) {
  session_register("user",$_REQUEST["user"]);
  $user = $_REQUEST["user"];
  $smarty->assign_by_ref('user',$user);
  header("location: $tikiIndex");
  die;
} else {
  if($feature_challenge == 'y') {
    $chall=$userlib->generate_challenge();
    $_SESSION["challenge"]=$chall;
    $smarty->assign('challenge',$chall);
  }  
  $smarty->assign('msg',tra("Invalid username or password"));
  $smarty->display('error.tpl');
}
?>