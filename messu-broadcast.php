<?php

// $Header: /cvsroot/tikiwiki/tiki/messu-broadcast.php,v 1.19 2004-03-28 07:32:22 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if ($feature_messages != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_messages");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_broadcast != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['to']))
	$_REQUEST['to'] = '';

if (!isset($_REQUEST['cc']))
	$_REQUEST['cc'] = '';

if (!isset($_REQUEST['bcc']))
	$_REQUEST['bcc'] = '';

if (!isset($_REQUEST['subject']))
	$_REQUEST['subject'] = '';

if (!isset($_REQUEST['body']))
	$_REQUEST['body'] = '';

if (!isset($_REQUEST['priority']))
	$_REQUEST['priority'] = 3;

$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);

$smarty->assign('mid', 'messu-broadcast.tpl');

$smarty->assign('sent', 0);

if (isset($_REQUEST['reply']) || isset($_REQUEST['replyall'])) {
	$messulib->flag_message($user, $_REQUEST['msgId'], 'isReplied', 'y');
}

if (isset($_REQUEST['group'])) {
	if ($_REQUEST['group'] == 'all') {
		$a_all_users = $userlib->get_users(0, -1, 'login_desc', '');

		$all_users = array();

		foreach ($a_all_users['data'] as $a_user) {
			$all_users[] = $a_user['user'];
		}
	} else {
		$all_users = $userlib->get_group_users($_REQUEST['group']);
	}
}

if (isset($_REQUEST['send'])) {
	check_ticket('messu-broadcast');
	$smarty->assign('sent', 1);

	$message = '';

	// Validation:
	// must have a subject or body non-empty (or both)
	if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
		$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

		$smarty->display("tiki.tpl");
		die;
	}

	// Remove invalid users from the to, cc and bcc fields
	$users = array();

	foreach ($all_users as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->get_user_preference($a_user, 'allowMsgs', 'y')) {
					$users[] = $a_user;
				} else {
					// TODO: needs translation as soon as there is a solution for strings with embedded variables
					$message .= "User $a_user can not receive messages<br />";
				}
			} else {
				$message .= tra("Invalid user"). "$a_user<br />";
			}
		}
	}

	$users = array_unique($users);

	// Validation: either to, cc or bcc must have a valid user
	if (count($users) > 0) {
		$message .= tra("Message will be sent to: "). implode(',', $users). "<br />";
	} else {
		$message = tra('ERROR: No valid users to send the message');

		$smarty->assign('message', $message);
		$smarty->display("tiki.tpl");
		die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message($a_user, $user, $a_user, '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
	}

	$smarty->assign('message', $message);
}
ask_ticket('messu-broadcast');

$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

$section = 'user_messages';
include_once ('tiki-section_options.php');

include_once ('tiki-mytiki_shared.php');
$smarty->display("tiki.tpl");

?>
