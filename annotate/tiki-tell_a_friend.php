<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
// To include a link in your tpl do
//<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>

$smarty->assign('headtitle', tra('Send a link to a friend '));
if (empty($_REQUEST['report'])) {
	$access->check_feature('feature_tell_a_friend');
	$access->check_permission('tiki_p_tell_a_friend');
}

if (!empty($_REQUEST['report']) && $_REQUEST['report'] == 'y') {
	$access->check_feature('feature_site_report', '', 'look');
	$access->check_permission('tiki_p_site_report');
}

if (empty($_REQUEST['url']) && !empty($_SERVER['HTTP_REFERER'])) {
	$u = parse_url($_SERVER['HTTP_REFERER']);
	if ($u['host'] != $_SERVER['SERVER_NAME']) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	$_REQUEST['url'] = $_REQUEST['HTTP_REFERER'];
}
if (empty($_REQUEST['url'])) {
	$smarty->assign('msg', tra('missing parameters'));
	$smarty->display('error.tpl');
	die;
}
$_REQUEST['url'] = urldecode($_REQUEST['url']);
if (strstr($_REQUEST['url'], 'tiki-tell_a_friend.php')) {
	$_REQUEST['url'] = preg_replace('/.*tiki-tell_a_friend.php\?url=/', '', $_REQUEST['url']);
	header('location: tiki-tell_a_friend.php?url=' . $_REQUEST['url']);
}
$url_for_friend = $tikilib->httpPrefix( true ) . $_REQUEST['url'];
$smarty->assign('url', $_REQUEST['url']);
$smarty->assign('prefix', $tikilib->httpPrefix( true ));
$errors = array();
if (isset($_REQUEST['send'])) {
	check_ticket('tell-a-friend');
	if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
		$errors[] = $captchalib->getErrors();
	}
	if (empty($_REQUEST['report']) || $_REQUEST['report'] != 'y') {
		$emails = explode(',', str_replace(' ', '', $_REQUEST['addresses']));
	} else {
		$email = !empty($prefs['feature_site_report_email']) ? $prefs['feature_site_report_email'] : (!empty($prefs['sender_email']) ? $prefs['sender_email'] : '');
		if (empty($email)) {
			$errors[] = tra("The mail can't be sent. Contact the administrator");
		}
		$_REQUEST['addresses'] = $email;
		$emails[] = $email;
	}
	foreach($emails as $email) {
		include_once ('lib/registration/registrationlib.php');
		if (function_exists('validate_email')) {
			$ok = validate_email($email, $prefs['validateEmail']);
		} else {
			$ret = $registrationlib->SnowCheckMail($email, '', 'mini');
			$ok = $ret[0];
		}
		if (!$ok) {
			if (isset($_REQUEST['report']) && $_REQUEST['report'] == 'y') $errors[] = tra("The mail can't be sent. Contact the administrator");
			else $errors[] = tra('One of the email addresses you typed is invalid') . ': ' . $email;
		}
	}
	if (empty($_REQUEST['email'])) {
		$errors[] = tra('Your email is mandatory');
	} else {
		$smarty->assign_by_ref('email', $_REQUEST['email']);
		if (validate_email($_REQUEST['email'])) {
			$from = str_replace(array("\r", "\n"), '', $_REQUEST['email']);
		} else {
			$errors[] = tra('Invalid email') . ': ' . $_REQUEST['email'];
		}
	}
	if (!empty($_REQUEST['addresses'])) $smarty->assign('addresses', $_REQUEST['addresses']);
	if (!empty($_REQUEST['name'])) $smarty->assign('name', $_REQUEST['name']);
	if (!empty($_REQUEST['comment'])) $smarty->assign('comment', $_REQUEST['comment']);
	if (empty($errors)) {
		include_once ('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$smarty->assign_by_ref('mail_site', $_SERVER['SERVER_NAME']);
		$mail->setFrom($from);
		$mail->setHeader("Return-Path", "<$from>");
		$mail->setHeader("Reply-To", "<$from>");
		if (isset($_REQUEST['report']) && $_REQUEST['report'] == 'y') {
			$subject = tra('Report to the webmaster', $prefs['site_language']);
		} else {
			$subject = $smarty->fetch('mail/tellAFriend_subject.tpl');
		}

		if( $prefs['auth_token_tellafriend'] == 'y' && $prefs['auth_token_access'] == 'y' && isset($_POST['share_access']) ) {
			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build( $prefs );

			$url_for_friend = $tokenlib->includeToken( $url_for_friend, $globalperms->getGroups() );
		}

		$smarty->assign( 'url_for_friend', $url_for_friend );
		$txt = $smarty->fetch('mail/tellAFriend.tpl');
		$mail->setSubject($subject);
		$mail->setText($txt);
		$mail->buildMessage();
		$ok = true;
		foreach($emails as $email) {
			$ok = $ok && $mail->send(array($email));
		}
		if ($ok) {
			$access->redirect( $_REQUEST['url'], tra('Your link was sent.') );
		} else {
			$errors = tra("The mail can't be sent. Contact the administrator");
		}
	}
	$smarty->assign_by_ref('errors', $errors);
	$smarty->assign('errortype', 'no_redirect_login');
} else {
	$smarty->assign_by_ref('name', $user);
	$smarty->assign('email', $userlib->get_user_email($user));
}
if (!empty($_REQUEST['report'])) {
	$smarty->assign_by_ref('report', $_REQUEST['report']);
}
ask_ticket('tell-a-friend');
$smarty->assign('mid', 'tiki-tell_a_friend.tpl');
$smarty->display('tiki.tpl');
