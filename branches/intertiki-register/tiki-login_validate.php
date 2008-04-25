<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-login_validate.php,v 1.25.2.3 2008-03-22 12:21:03 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['validateUsers'] != 'y' && $prefs['validateRegistration'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": validateUsers");

	$smarty->display("error.tpl");
	die;
}


$isvalid = false;
if (isset($_REQUEST["user"])) {
	if (isset($_REQUEST["pass"])) {
		list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_REQUEST["pass"],'','',true);
	} else {
		$error = PASSWORD_INCORRECT;
	}
} else {
	$error = USER_NOT_FOUND;
}  

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if ($isvalid) {
	$info = $userlib->get_user_info($_REQUEST['user']);
	if ($info['waiting'] == 'a' && $prefs['validateUsers'] == 'y') { // admin validating -> need user email validation now
		$userlib->send_validation_email($_REQUEST['user'], $info['valid'], $info['email'], '', 'y');
		$userlib->change_user_waiting($_REQUEST['user'], 'u');
		$logslib->add_log('register','admin validation '.$_REQUEST['user']);
	} elseif ($info['waiting'] == 'a' && $prefs['validateRegistration'] == 'y') { //admin validating -> user can log in
		$userlib->confirm_user($_REQUEST['user']);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$foo1 = str_replace('tiki-login_validate','tiki-login_scr',$foo['path']);
		$machine =$tikilib->httpPrefix().$foo1;
		$smarty->assign('mail_machine',$machine);
		$smarty->assign('mail_site',$_SERVER['SERVER_NAME']);
		$smarty->assign('mail_user', $_REQUEST['user']);
		$email = $userlib->get_user_email($_REQUEST['user']);
		include_once("lib/webmail/tikimaillib.php");
		$mail = new TikiMail();
		$mail->setText($smarty->fetch('mail/moderate_activation_mail.tpl'));					
		$mail->setSubject($smarty->fetch('mail/moderate_activation_mail_subject.tpl'));					
		$mail->send(array($email));
		$userlib->change_user_waiting($_REQUEST['user'], NULL);
		$logslib->add_log('register','validated account '.$_REQUEST['user']);
	} elseif (empty($user)) {
		$userlib->confirm_user($_REQUEST['user']);
		$user = $_REQUEST['user'];
		$_SESSION["$user_cookie_site"] = $user;
		$smarty->assign('user', $user);
	}
	$smarty->assign('msg', tra("Account validated successfully."));
	$smarty->assign('mid', 'tiki-information.tpl');
	$smarty->display("tiki.tpl");
	die;
} else {
	if ($error == PASSWORD_INCORRECT)
		$error = tra("Invalid password");
	else if ($error == USER_NOT_FOUND)
		$error = tra("Invalid username");
	else if ($error == ACCOUNT_DISABLED)
		$error = tra("Account disabled");
	else if ($error == USER_AMBIGOUS)
		$error = tra("You must use the right case for your user name");
	else
		$error= tra('Invalid username or password');
	$smarty->assign('msg', $error);
	$smarty->display("error.tpl");
}

?>
