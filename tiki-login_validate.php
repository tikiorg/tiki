<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_validate.php,v 1.13 2005-03-12 16:49:00 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/userslib/userslib_admin.php');

$isvalid = false;
if (isset($_REQUEST["user"]) && isset($_REQUEST["pass"])) {
	$isvalid = $userslibadmin->validate_user($_REQUEST["user"], $_REQUEST["pass"], '', '');
}
if ($isvalid) {
	//session_register("user",$_REQUEST["user"]); 
	$userlib->confirm_user($_REQUEST['user']);
	if ($tikilib->get_preference('validateRegistration','n') == 'y') {
		$email = $userlib->get_user_email($user);
		include_once("lib/webmail/tikimaillib.php");
		$mail = new TikiMail();
		$mail->setText($smarty->fetch('mail/moderate_welcome_mail.tpl'));					
		$mail->setSubject($smarty->fetch('mail/moderate_welcome_mail_subject.tpl'));					
		$mail->send(array($email));
		$logslib->add_log('register','validated account '.$user);
	} else {
		$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	}
	header ("location: $tikiIndex");
	die;
} else {
	$smarty->assign('msg', tra("Invalid username or password"));
	$smarty->display("error.tpl");
}

?>
