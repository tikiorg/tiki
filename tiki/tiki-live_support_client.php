<?php
// Initialization
require_once('tiki-setup.php');
include('lib/live_support/lslib.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0

$smarty->assign('senderId',md5(uniqid('.')));
if($user) {
  $smarty->assign('user_email',$tikilib->get_user_email($user));
}
// Display the template
$smarty->display("tiki-live_support_client.tpl");
?>