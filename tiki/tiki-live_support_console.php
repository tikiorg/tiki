<?php
// Initialization
require_once('tiki-setup.php');
include('lib/live_support/lslib.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0


$max_active_request = $lslib->get_max_active_request();
$smarty->assign('new_requests','n');
if(!isset($_SESSION['max_request'])) {
	$_SESSION['max_request']=$max_active_request;
	$smarty->assign('new_requests','y');
} else {
	if($max_active_request > $_SESSION['max_request']) {
		$_SESSION['max_request'] = $max_active_request;
		$smarty->assign('new_requests','y');
	}
}
$requests = $lslib->get_requests('active');
$smarty->assign('requests',$requests);
$smarty->assign('chats',$lslib->get_requests('op_accepted'));
$smarty->assign('last',$lslib->get_last_request());
if(isset($_REQUEST['status'])) {
	$lslib->set_operator_status($user,$_REQUEST['status']);
}
$smarty->assign('status',$lslib->get_operator_status($user));

// Display the template
$smarty->display("tiki-live_support_console.tpl");
?>