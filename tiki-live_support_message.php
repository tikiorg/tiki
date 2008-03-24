<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/live_support/lsadminlib.php');

if ($prefs['feature_live_support'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_live_support");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('sent', 'n');
$smarty->assign('nomsg', 'y');

if (isset($_REQUEST['save'])) {
	$lsadminlib->post_support_message($_REQUEST['username'],
		$user, $_REQUEST['user_email'], $_REQUEST['data'], $_REQUEST['priority'], $_REQUEST['module'], 'o', '');

	$smarty->assign('sent', 'y');
}

if ($user) {
	$smarty->assign('user_email', $userlib->get_user_email($user));
}

$smarty->assign('modules', $lsadminlib->get_modules());
// Display the template
$smarty->display("tiki-live_support_client.tpl");

?>
