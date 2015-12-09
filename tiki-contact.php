<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$messulib = TikiLib::lib('message');

// This feature needs both 'feature_contact' and 'feature_messages' to work
$access->check_feature(array('feature_contact', 'feature_messages'));

$auto_query_args = array();

if ($user) {
	$access->check_permission('tiki_p_messages');
} else {
	$access->check_feature('contact_anon');
}

$smarty->assign('sent', 0);

$priority = 3;
$from = $user ? $user : '';
$subject = '';
$body = '';
if (isset($_REQUEST['send'])) {
	if (isset($_REQUEST['priority'])) {
		$priority = $_REQUEST['priority'];
	}
	if (!$user && validate_email($_REQUEST['from'])) {
		$from =  'tiki-contact.php';
		$body .= tra('From') . " " . $_REQUEST['from'] . ":\n";
	}
	if (isset($_REQUEST['subject'])) {
		$subject =  $_REQUEST['subject'];
	}
	if (isset($_REQUEST['body'])) {
		$body .=  $_REQUEST['body'];
	}
}

if (isset($_REQUEST['send'])) {
	// Validation:
	// must have a subject or body non-empty (or both)
	$hasContent = !empty($_REQUEST['subject']) || !empty($_REQUEST['body']);

	$failsCaptcha = !$user && $prefs['feature_antibot'] == 'y' && !$captchalib->validate();
	if (!$hasContent || empty($from) || $failsCaptcha) {
		if (!$hasContent) {
			$message = tra("You must include a subject or a message.");
		} elseif (empty($from)) {
			$message = tra("You must make sure to have a valid email address in the From field.");
		} else {
			$message = $captchalib->getErrors();
		}
		$smarty->assign('errorMessage', $message);
	} else {
		$access->check_ticket();
		$body = tr("%0 sent you a message:", $from) . "\n" . $body;
		$messulib->post_message(
			$prefs['contact_user'],
			$from,
			$_REQUEST['to'],
			'',
			$_REQUEST['subject'],
			$body,
			$priority
		);
		$contact_name = $userlib->get_user_preference($prefs['contact_user'], 'realName');
		if ($contact_name == '') $contact_name = $prefs['contact_user'];
		$message = tra('Message sent to'). ': ' . $contact_name . '<br />';
		$smarty->assign('sent', 1);
		$smarty->assign('message', $message);
	}
}

$email = $userlib->get_user_email($prefs['contact_user']);
if ($email == '') $email = $userlib->get_admin_email();
$smarty->assign('email0', $email);
$email = TikiMail::scrambleEmail($email, $tikilib->get_user_preference('admin', "email is public"));
$smarty->assign('email', $email);

$smarty->assign('priority', $priority);
$smarty->assign('from', $from);
$smarty->assign('subject', $subject);
$smarty->assign('body', $body);

$smarty->assign('mid', 'tiki-contact.tpl');
$smarty->display("tiki.tpl");
