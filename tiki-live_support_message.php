<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/live_support/lsadminlib.php');

if($feature_live_support != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$smarty->assign('sent','n');
$smarty->assign('nomsg','y');

if(isset($_REQUEST['save'])) {
	$lsadminlib->post_support_message($_REQUEST['username'],$user,$_REQUEST['user_email'],$_REQUEST['data'],$_REQUEST['priority'],$_REQUEST['module'],'o','');
	$smarty->assign('sent','y');
}

if($user) {
  $smarty->assign('user_email',$tikilib->get_user_email($user));
}

$smarty->assign('modules',$lsadminlib->get_modules());
// Display the template
$smarty->display("tiki-live_support_message.tpl");
?>