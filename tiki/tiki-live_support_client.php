<?php
// Initialization
require_once('tiki-setup.php');
include('lib/live_support/lslib.php');

$smarty->assign('senderId',md5(uniqid('.')));
if($user) {
  $smarty->assign('user_email',$tikilib->get_user_email($user));
}
// Display the template
$smarty->display("tiki-live_support_client.tpl");
?>