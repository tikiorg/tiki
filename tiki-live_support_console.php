<?php
// Initialization
require_once('tiki-setup.php');
include('lib/live_support/lslib.php');

$smarty->assign('requests',$lslib->get_active_requests());
$smarty->assign('last',$lslib->get_last_request());

// Display the template
$smarty->display("tiki-live_support_console.tpl");
?>