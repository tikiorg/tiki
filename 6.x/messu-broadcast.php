<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'user_messages';
require_once ('tiki-setup.php');
include_once ('lib/messu/messulib.php');
$access->check_user($user);
$access->check_feature('feature_messages');
$access->check_permission('tiki_p_broadcast');
$auto_query_args = array('to', 'cc', 'bcc', 'subject', 'body', 'priority', 'replyto_hash', 'groupbr');
if (!isset($_REQUEST['to'])) $_REQUEST['to'] = '';
if (!isset($_REQUEST['cc'])) $_REQUEST['cc'] = '';
if (!isset($_REQUEST['bcc'])) $_REQUEST['bcc'] = '';
if (!isset($_REQUEST['subject'])) $_REQUEST['subject'] = '';
if (!isset($_REQUEST['body'])) $_REQUEST['body'] = '';
if (!isset($_REQUEST['priority'])) $_REQUEST['priority'] = 3;
if (!isset($_REQUEST['replyto_hash'])) $_REQUEST['replyto_hash'] = '';
$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);
$smarty->assign('replyto_hash', $_REQUEST['replyto_hash']);
$smarty->assign('mid', 'messu-broadcast.tpl');
$smarty->assign('sent', 0);
if (isset($_REQUEST['groupbr'])) {
	if ($_REQUEST['groupbr'] == 'all' && $tiki_p_broadcast_all == 'y') {
		$a_all_users = $userlib->get_users(0, -1, 'login_desc', '');
		$all_users = array();
		foreach($a_all_users['data'] as $a_user) {
			$all_users[] = $a_user['user'];
		}
	} else {
		$all_users = $userlib->get_group_users($_REQUEST['groupbr']);
	}
	$smarty->assign_by_ref('groupbr', $_REQUEST['groupbr']);
}
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

if (isset($_REQUEST['send']) || isset($_REQUEST['preview'])) {
	check_ticket('messu-broadcast');
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
	foreach($all_users as $a_user) {
		if (!empty($a_user)) {
			if ($userlib->user_exists($a_user)) {
				if (!$userlib->user_has_permission($a_user, 'tiki_p_messages')) {
					$message .= sprintf(tra('User %s does not have the permission'), htmlspecialchars($a_user)). "<br />" ;
				} elseif ($tikilib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					$users[] = $a_user;
				} else {
					$message.= sprintf(tra("User %s does not want to receive messages"), htmlspecialchars($a_user)). "<br />" ;
				}
			} else {
				$message.= tra("Invalid user") . "$a_user<br />";
			}
		}
	}
	$users = array_unique($users);
	// Validation: either to, cc or bcc must have a valid user
	if (count($users) > 0) {
		$users_formatted = array();
		foreach ($users as $rawuser)
			$users_formatted[] = htmlspecialchars($rawuser);
		if (isset($_REQUEST['send'])) {
			$message .= tra('The message has been sent to:').' ';
		} else {
			$message .= tra('The message will be sent to:').' ';
		}
		$message .= implode(',', $users_formatted) . "<br />";
	} else {
		$message .= tra('ERROR: No valid users to send the message');
		$smarty->assign('message', $message);
		$smarty->display("tiki.tpl");
		die;
	}
	if (isset($_REQUEST['send'])) {
		$smarty->assign('sent', 1);
		// Insert the message in the inboxes of each user
		foreach($users as $a_user) {
			$messulib->post_message($a_user, $user, $a_user, '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
			// if this is a reply flag the original messages replied to
			if ($_REQUEST['replyto_hash'] <> '') {
				$messulib->mark_replied($a_user, $_REQUEST['replyto_hash']);
			}
		}
		// Insert a copy of the message in the sent box of the sender
		$messulib->save_sent_message($user, $user, $_REQUEST['groupbr'], $_REQUEST['cc'], $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority'], $_REQUEST['replyto_hash']);
		$smarty->assign('message', $message);
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib->add_action('Posted', '', 'message', 'add=' . strlen($_REQUEST['body']));
		}
	} else {
		$smarty->assign_by_ref('message', $message);
		$smarty->assign('preview', 1);
	}
}
ask_ticket('messu-broadcast');
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
$smarty->display("tiki.tpl");
