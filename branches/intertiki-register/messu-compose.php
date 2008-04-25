<?php

// $Id: /cvsroot/tikiwiki/tiki/messu-compose.php,v 1.40 2007-10-12 07:55:23 nyloth Exp $

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

if ($tiki_p_messages != 'y') {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

if ($prefs['allowmsg_is_optional'] == 'y') {
	if ($tikilib->get_user_preference($user, 'allowMsgs', 'y') != 'y') {
	$smarty->assign('msg', tra("You have to be able to receive messages in order to send them. Goto your user preferences and enable 'Allow messages from other users'"));
	$smarty->display("error.tpl");
	die;
	}
}

if (($prefs['messu_sent_size']>0) && ($messulib->count_messages($user, 'sent')>=$prefs['messu_sent_size']) ) {
	$smarty->assign('msg', tra('Sent box is full. Archive or delete some sent messages first if you want to send more messages.'));
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

if (!isset($_REQUEST['replyto_hash']))
	$_REQUEST['replyto_hash'] = '';

if (!isset($_REQUEST['priority']))
	$_REQUEST['priority'] = 3;

// Strip Re:Re:Re: from subject
if(!empty($_REQUEST['reply'])||!empty($_REQUEST['replyall'])) {
	$_REQUEST['subject'] = tra("Re:") . ereg_replace("^(".tra("Re:").")+", "", $_REQUEST['subject']);
	$smarty->assign('reply', 'y');
}

foreach( array( 'to', 'cc', 'bcc' ) as $dest )
	if( is_array( $_REQUEST[$dest] ) )
		$_REQUEST[$dest] = implode( ', ', array_filter( $_REQUEST[$dest], 'ctype_alnum' ) );

$smarty->assign('to', $_REQUEST['to']);
$smarty->assign('cc', $_REQUEST['cc']);
$smarty->assign('bcc', $_REQUEST['bcc']);
$smarty->assign('subject', $_REQUEST['subject']);
$smarty->assign('body', $_REQUEST['body']);
$smarty->assign('priority', $_REQUEST['priority']);
$smarty->assign('replyto_hash', $_REQUEST['replyto_hash']);

$smarty->assign('mid', 'messu-compose.tpl');

$smarty->assign('sent', 0);

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
	$arr_to = preg_split('/\s*(?<!\\\);\s*/', $_REQUEST['to']);
	$arr_cc = preg_split('/\s*(?<!\\\);\s*/', $_REQUEST['cc']);
	$arr_bcc = preg_split('/\s*(?<!\\\);\s*/', $_REQUEST['bcc']);

	// Remove invalid users from the to, cc and bcc fields
	$users = array();

	foreach ($arr_to as $a_user) {
		if (!empty($a_user)) {
			$a_user = str_replace('\\;', ';', $a_user);
			if ($userlib->user_exists($a_user)) {
				// mail only to users with activated message feature
			  if ($prefs['allowmsg_is_optional'] != 'y' || $tikilib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					// only send mail if nox mailbox size is defined or not reached yet
					if (($messulib->count_messages($a_user)<$prefs['messu_mailbox_size']) || ($prefs['messu_mailbox_size']==0)) {
						$users[] = $a_user;
					} else {
						$message .= sprintf(tra("User %s can not receive messages, mailbox is full"),$a_user)."<br />";
					}
				} else {
					$message .= sprintf(tra("User %s can not receive messages"),$a_user)."<br />";
				}
			} else {
				$message .= sprintf(tra("Invalid user: %s"),$a_user)."<br />";
			}
		}
	}

	foreach ($arr_cc as $a_user) {
		if (!empty($a_user)) {
			$a_user = str_replace('\\;', ';', $a_user);
			if ($userlib->user_exists($a_user)) {
				// mail only to users with activated message feature
			  if ($prefs['allowmsg_is_optional'] != 'y' || $tikilib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					// only send mail if nox mailbox size is defined or not reached yet
					if (($messulib->count_messages($a_user)<$prefs['messu_mailbox_size']) || ($prefs['messu_mailbox_size']==0)) {
						$users[] = $a_user;
					} else {
						$message .= sprintf(tra("User %s can not receive messages, mailbox is full"),$a_user)."<br />";
					}
				} else {
					$message .= sprintf(tra("User %s can not receive messages"),$a_user)."<br />";
				}
			} else {
				$message .= sprintf(tra("Invalid user: %s"),$a_user)."<br />";
			}
		}
	}

	foreach ($arr_bcc as $a_user) {
		if (!empty($a_user)) {
			$a_user = str_replace('\\;', ';', $a_user);
			if ($userlib->user_exists($a_user)) {
				// mail only to users with activated message feature
				if ($prefs['allowmsg_is_optional'] != 'y' || $tikilib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					// only send mail if nox mailbox size is defined or not reached yet
					if (($messulib->count_messages($a_user)<$prefs['messu_mailbox_size']) || ($prefs['messu_mailbox_size']==0)) {
						$users[] = $a_user;
					} else {
						$message .= sprintf(tra("User %s can not receive messages, mailbox is full"),$a_user)."<br />";
					}
				} else {
					$message .= sprintf(tra("User %s can not receive messages"),$a_user)."<br />";
				}
			} else {
				$message .= sprintf(tra("Invalid user: %s"),$a_user)."<br />";
			}
		}
	}

	$users = array_unique($users);

	// Validation: either to, cc or bcc must have a valid user
	if (count($users) > 0) {
		$message .= tra("Message will be sent to: "). implode(',', $users). "<br />";
	} else {
		$message .= tra('ERROR: No valid users to send the message');
		$smarty->assign('message', $message);
		$smarty->display("tiki.tpl");
		die;
	}

	// Insert the message in the inboxes of each user
	foreach ($users as $a_user) {
		$messulib->post_message(
			$a_user, $user, $_REQUEST['to'], $_REQUEST['cc'], $_REQUEST['subject'], $_REQUEST['body'],
			$_REQUEST['priority'], $_REQUEST['replyto_hash']);
    		if ($prefs['feature_score'] == 'y') {
	            $tikilib->score_event($user, 'message_send');
			    $tikilib->score_event($a_user, 'message_receive');
	        }
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
		include_once('lib/logs/logslib.php');
		if (isset($_REQUEST['reply']) && $_REQUEST['reply'] == 'y')
			$logslib->add_action('Replied', '', 'message', 'add='.$tikilib->strlen_quoted($_REQUEST['body']));
		else
			$logslib->add_action('Posted', '', 'message', 'add='.strlen($_REQUEST['body']));
	}
}
$allowMsgs = $prefs['allowmsg_is_optional'] != 'y' || $tikilib->get_user_preference($user, 'allowMsgs', 'y');
$smarty->assign('allowMsgs', $allowMsgs);

include_once ('tiki-section_options.php');

ask_ticket('messu-compose');

include_once ('tiki-mytiki_shared.php');

$smarty->display("tiki.tpl");

?>
