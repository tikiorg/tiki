<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/live_support/lslib.php');
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s"). " GMT"); // always modified
header ("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header ("Pragma: no-cache");                                   // HTTP/1.0

if ($prefs['feature_live_support'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_live_support");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_live_support_admin != 'y' && !$lsadminlib->is_operator($user)) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$max_active_request = $lslib->get_max_active_request();
$smarty->assign('new_requests', 'n');

if (!isset($_SESSION['max_request'])) {
	$_SESSION['max_request'] = $max_active_request;

	$smarty->assign('new_requests', 'y');
} else {
	if ($max_active_request > $_SESSION['max_request']) {
		$_SESSION['max_request'] = $max_active_request;

		$smarty->assign('new_requests', 'y');
	}
}

$requests = $lslib->get_requests('active');
$smarty->assign('requests', $requests);
$smarty->assign('chats', $lslib->get_requests('op_accepted'));
$smarty->assign('last', $lslib->get_last_request());

if (isset($_REQUEST['status'])) {
	$lslib->set_operator_status($user, $_REQUEST['status']);
}

$smarty->assign('status', $lslib->get_operator_status($user));

// Display the template
$smarty->display("tiki-live_support_console.tpl");

?>
