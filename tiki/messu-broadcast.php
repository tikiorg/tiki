<?php

// $Header: /cvsroot/tikiwiki/tiki/messu-broadcast.php,v 1.30.2.1 2007-11-22 17:09:02 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'user_messages';
require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');

if (!$user) {
	if ($prefs['feature_redirect_on_error'] == 'y') {
		header('location: '.$prefs['tikiIndex']);
		die;
	} else {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
	}
}

if ($prefs['feature_messages'] != 'y') {
	if ($prefs['feature_redirect_on_error'] == 'y') {
		header('location: '.$prefs['tikiIndex']);
		die;
	} else {
	$smarty->assign('msg', tra("This feature is disabled").": feature_messages");

	$smarty->display("error.tpl");
	die;
	}
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

if (!isset($_REQUEST['replyto_hash']))
	$_REQUEST['replyto_hash'] = '';

$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);
$smarty->assign('replyto_hash', $_REQUEST['replyto_hash']);

$smarty->assign('mid', 'messu-broadcast.tpl');

$smarty->assign('sent', 0);

if (isset($_REQUEST['group'])) {
	if ($_REQUEST['group'] == 'all' && $tiki_p_broadcast_all == 'y') {
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
			if ($userlib->user_exists($a_user)) {
				if ($tikilib->get_user_preference($a_user, 'allowMsgs', 'y')) {
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
		$message .= tra("Message sent to"). ': '.implode(',', $users). "<br />";
	} else {
		$message = tra('ERROR: No valid users to send the message');

		$smarty->assign('message', $message);
		$smarty->display("tiki.tpl");
		die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message($a_user, $user, $a_user, '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
		// if this is a reply flag the original messages replied to
		if ($_REQUEST['replyto_hash']<>'') {	
			$messulib->mark_replied($a_user, $_REQUEST['replyto_hash']);
		}
	}

	// Insert a copy of the message in the sent box of the sender
	$messulib->save_sent_message(
		$user, $user, $_REQUEST['to'], $_REQUEST['cc'], $_REQUEST['subject'], $_REQUEST['body'],
		$_REQUEST['priority'], $_REQUEST['replyto_hash']);

	$smarty->assign('message', $message);

	if ($prefs['feature_actionlog'] == 'y') {
		$logslib->add_action('Posted', '', 'message', 'add='.strlen($_REQUEST['body']));
	}
}
ask_ticket('messu-broadcast');

$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

include_once ('tiki-section_options.php');

include_once ('tiki-mytiki_shared.php');
$smarty->display("tiki.tpl");

?>
