<?php

// $Header: /cvsroot/tikiwiki/tiki/messu-compose.php,v 1.22 2005-01-01 00:16:15 damosoft Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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

if ($tiki_p_messages != 'y') {
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

// Strip Re:Re:Re: from subject
if(isset($_REQUEST['reply'])||isset($_REQUEST['replyall'])) {
	$_REQUEST['subject'] = tra("Re:") . ereg_replace("^(".tra("Re:").")+", "", $_REQUEST['subject']);
}

$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);

$smarty->assign('mid', 'messu-compose.tpl');

$smarty->assign('sent', 0);

if (isset($_REQUEST['reply']) || isset($_REQUEST['replyall'])) {
	$messulib->flag_message($user, $_REQUEST['msgId'], 'isReplied', 'y');
}

if (isset($_REQUEST['send'])) {
	check_ticket('messu-compose');
	
	$smarty->assign('sent', 1);

	$message = '';

	// Validation:
	// must have a subject or body non-empty (or both)
	if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
		$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

		$smarty->display("tiki.tpl");
		die;
	}

	// Parse the to, cc and bcc fields into an array
	$arr_to = preg_split('/\s*(,|\s)\s*/', $_REQUEST['to']);
	$arr_cc = preg_split('/\s*(,|\s)\s*/', $_REQUEST['cc']);
	$arr_bcc = preg_split('/\s*(,|\s)\s*/', $_REQUEST['bcc']);

	// Remove invalid users from the to, cc and bcc fields
	$users = array();

	foreach ($arr_to as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					$users[] = $a_user;
				} else {
					$message .= tra("User $a_user can not receive messages")."<br />";
				}
			} else {
				$message .= tra("Invalid user: $a_user")."<br />";
			}
		}
	}

	foreach ($arr_cc as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					$users[] = $a_user;
				} else {
					$message .= tra("User $a_user can not receive messages")."<br />";
				}
			} else {
				$message .= tra("Invalid user: $a_user")."<br />";
			}
		}
	}

	foreach ($arr_bcc as $a_user) {
		if (!empty($a_user)) {
			if ($messulib->user_exists($a_user)) {
				if ($messulib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					$users[] = $a_user;
				} else {
					$message .= tra("User $a_user can not receive messages")."<br />";
				}
			} else {
				$message .= tra("Invalid user: $a_user")."<br />";
			}
		}
	}

	$users = array_unique($users);

	// Validation: either to, cc or bcc must have a valid user
	if (count($users) <= 0) {
		$message .= tra('ERROR: No valid users to send the message');

		$smarty->assign('message', $message);
		$smarty->display("tiki.tpl");
		die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message(
			$a_user, $user, $_REQUEST['to'], $_REQUEST['cc'], $_REQUEST['subject'], $_REQUEST['body'],
			$_REQUEST['priority']);
        		if ($feature_score == 'y') {
		            $tikilib->score_event($user, 'message_send');
			    $tikilib->score_event($a_user, 'message_receive');
		        }

	}
	$message .= tra("Message sent to: "). implode(',', $users). "<br />";
	$smarty->assign('message', $message);
}

$section = 'user_messages';
include_once ('tiki-section_options.php');

ask_ticket('messu-compose');

include_once ('tiki-mytiki_shared.php');

$smarty->display("tiki.tpl");

?>
