<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-tell_a_friend.php,v 1.3 2007-03-19 13:51:07 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// To include a link in your tpl do
//<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>

if ($feature_tell_a_friend != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_tell_a_friend');
	$smarty->display('error.tpl');
	die;
}
if ($tiki_p_tell_a_friend != 'y') {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}
if (empty($_REQUEST['url'])) {
	$smarty->assign('msg', tra('missing parameters'));
	$smarty->display('error.tpl');
	die;
}
$_REQUEST['url'] = preg_replace('/.*tiki-tell_a_friend.php\?url=/', '', $_REQUEST['url']);
$smarty->assign('url', urldecode($_REQUEST['url']));

$smarty->assign('prefix', $tikilib->httpPrefix());

$errors = array();
if (isset($_REQUEST['send'])) {
	check_ticket('tell-a-friend');
	$emails = explode(',', $_REQUEST['addresses']);
	foreach ($emails as $email) {
		include_once('lib/registration/registrationlib.php');
		include_once('lib/mods/modslib.php');
		if (newer($dbversion_tiki, 1.10) >= 0) {
			$ok = validate_email($email, 'y');
		} else {
			$ret = $registrationlib->SnowCheckMail($email,'','mini');
			$ok = $ret[0];
		}
		if (!$ok) {
			$errors[] = $email;
		}
	}
	if (!empty($_REQUEST['addresses']))
		$smarty->assign('addresses', $_REQUEST['addresses']);
	if (!empty($_REQUEST['name']))
		$smarty->assign('name', $_REQUEST['name']);
	if (!empty($_REQUEST['comment']))
		$smarty->assign('comment', $_REQUEST['comment']);
	if (empty($errors)) {
		include_once ('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$smarty->assign_by_ref('mail_site', $_SERVER['SERVER_NAME']);
		$mail->setFrom($tikilib->get_preference('sender_email',''));
		$txt = $smarty->fetch('mail/tellAFriend_subject.tpl');
		$mail->setSubject($txt);
		$txt = $smarty->fetch('mail/tellAFriend.tpl');
		$mail->setText($txt);
		$mail->buildMessage();
		$mail->send($emails);
		$smarty->assign_by_ref('sent', $_REQUEST['addresses']);
		$smarty->assign('comment', '');
		$smarty->assign('addresses', '');
	} else {
		$smarty->assign_by_ref('errors', $errors);
	}
} else {
	$smarty->assign('name', $user);
	
}
ask_ticket('tell-a-friend');

$smarty->assign('mid', 'tiki-tell_a_friend.tpl');
$smarty->display('tiki.tpl');
?>