<?php
// Initialization
require_once('tiki-setup.php');
include('lib/live_support/lslib.php');

// We should receive the reqId here

// Accept the call (so the client polling for accept will be notified)
$senderId = md5(uniqId('.'));
$lslib->operator_accept($_REQUEST['reqId'],$user,$senderId);
$smarty->assign('reqId',$_REQUEST['reqId']);
$smarty->assign('senderId',$senderId);

// Display the template
$smarty->display("tiki-live_support_operator.tpl");
?>