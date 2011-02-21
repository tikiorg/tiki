<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');
include_once ('lib/userprefs/scrambleEmail.php');

// This feature needs both 'feature_contact' and 'feature_messages' to work
$access->check_feature(array('feature_contact', 'feature_messages'));

$auto_query_args = array();

if ($user == '') {
	$access->check_feature('contact_anon');
	
	$smarty->assign('sent', 0);
	if (isset($_REQUEST['send'])) {
		check_ticket('contact');

		$message = '';
		// Validation:
		// must have a subject or body non-empty (or both)
		if (empty($_REQUEST['subject']) && empty($_REQUEST['body']) || empty($_REQUEST['from'])) {
			$smarty->assign('message', tra('ERROR: you must include a subject or a message. You must also make sure to have a valid e-mail in the FROM field'));
			$smarty->assign('priority', $_REQUEST['priority']);
			
			if (!empty($_REQUEST['from'])) $smarty->assign_by_ref('from', $_REQUEST['from']);
			if (!empty($_REQUEST['subject'])) $smarty->assign_by_ref('subject', $_REQUEST['subject']);
			if (!empty($_REQUEST['body'])) $smarty->assign_by_ref('body', $_REQUEST['body']);
			if (!empty($_REQUEST['priority'])) $smarty->assign_by_ref('priority', $_REQUEST['priority']);
			
			$smarty->display("tiki.tpl");
			die;
		}
		if ($prefs['feature_antibot'] == 'y') {
			if(!$captchalib->validate()) {
				$smarty->assign('message', $captchalib->getErrors());
				$smarty->assign('errortype', 'no_redirect_login');
				if (!empty($_REQUEST['from'])) $smarty->assign_by_ref('from', $_REQUEST['from']);
				if (!empty($_REQUEST['subject'])) $smarty->assign_by_ref('subject', $_REQUEST['subject']);
				if (!empty($_REQUEST['body'])) $smarty->assign_by_ref('body', $_REQUEST['body']);
				if (!empty($_REQUEST['priority'])) $smarty->assign_by_ref('priority', $_REQUEST['priority']);
				$smarty->display("tiki.tpl");
				die;
			}
		}
		$smarty->assign('sent', 1);
		$messulib->post_message($prefs['contact_user'], $_REQUEST['from'], $_REQUEST['to'],
			'', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);
		$message = tra('Message sent to'). ': ' . $prefs['contact_user'] . '<br />';
		$smarty->assign('message', $message);
	}
} else {
	$access->check_permission('tiki_p_messages');
	$smarty->assign('sent', 0);

	if (isset($_REQUEST['send'])) {
		check_ticket('contact');


		$message = '';

		// Validation:
		// must have a subject or body non-empty (or both)
		if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
			$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

			$smarty->display("tiki.tpl");
			die;
		}
		$smarty->assign('sent', 1);
		$message = tra('Message sent to'). ': ' . $prefs['contact_user'] . '<br />';
		$messulib->post_message($prefs['contact_user'], $user, $_REQUEST['to'],
			'', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);

		$smarty->assign('message', $message);
	}
}

$email = $userlib->get_user_email($prefs['contact_user']);
if ($email == '') $email = $userlib->get_admin_email();
$smarty->assign('email0', $email);
$email = scrambleEmail($email, $tikilib->get_user_preference('admin', "email is public"));
$smarty->assign('email', $email);

$smarty->assign('priority', 3);
ask_ticket('contact');

$smarty->assign('mid', 'tiki-contact.tpl');
$smarty->display("tiki.tpl");
