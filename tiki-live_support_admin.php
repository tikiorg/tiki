<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/live_support/lsadminlib.php');
include_once ('lib/live_support/lslib.php');

if ($prefs['feature_live_support'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_live_support");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_live_support_admin != 'y' && !$lsadminlib->user_is_operator($user)) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('html', false);

if (isset($_REQUEST['show_html'])) {
	$html = '<a href="#" onclick=\'javascript:window.open("tiki-live_support_client.php","","menubar=,scrollbars=yes,resizable=yes,height=450,width=300");\'><img border="0" src="tiki-live_support_server.php?operators_online" alt="image" /></a>';

	$smarty->assign('html', $html);
}

if ($tiki_p_live_support_admin == 'y') {
	if (isset($_REQUEST['adduser'])) {
		check_ticket('ls-admin');
		$lsadminlib->add_operator($_REQUEST['user']);
	}
        if (isset($_REQUEST['offline'])) {
		check_ticket('ls-offline');
                $lslib->set_operator_status($_REQUEST['offline'],'offline');
        }

	if (isset($_REQUEST['removeuser'])) {
		$area = 'dellsuser';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$lsadminlib->remove_operator($_REQUEST['removeuser']);
		} else {
			key_get($area);
		}
	}
}

// Get the list of operators
$online_operators = $lsadminlib->get_operators('online');
$offline_operators = $lsadminlib->get_operators('offline');
$smarty->assign_by_ref('online_operators', $online_operators);
$smarty->assign_by_ref('offline_operators', $offline_operators);

// Get the list of users
if (!isset($_REQUEST['find_users']))
	$_REQUEST['find_users'] = '';

$users = $userlib->get_users(0, -1, 'login_asc', $_REQUEST['find_users']);

$ok_users = array();

$temp_max = count($users['data']);
for ($i = 0; $i < $temp_max; $i++) {
	foreach ($online_operators as $op) {
		if ($op['user'] == $users['data'][$i]['user']) {
			unset ($users[$i]);
		}
	}

	foreach ($offline_operators as $op) {
		if (isset($users['data'][$i]) && $op['user'] == $users['data'][$i]['user']) {
			unset ($users['data'][$i]);
		}
	}

	if (isset($users['data'][$i])) {
		$ok_users[] = $users['data'][$i];
	}
}

$smarty->assign_by_ref('users', $ok_users);
ask_ticket('ls-admin');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-live_support_admin.tpl');
$smarty->display("tiki.tpl");

?>
