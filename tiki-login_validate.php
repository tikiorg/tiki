<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_validate.php,v 1.15 2005-07-08 20:17:24 xavidp Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

$isvalid = false;
if (isset($_REQUEST["user"])) {
	if (isset($_REQUEST["pass"])) {
		$isvalid = $userlib->validate_user($_REQUEST["user"], $_REQUEST["pass"],'','');
	}
}	
if ($isvalid) {
	//session_register("user",$_REQUEST["user"]); 
	$userlib->confirm_user($_REQUEST['user']);
	if ($tikilib->get_preference('validateRegistration','n') == 'y') {
		$email = $userlib->get_user_email($user);
		include_once("lib/webmail/tikimaillib.php");
		$mail = new TikiMail();
		$mail->setText($smarty->fetch('mail/moderate_validation_mail.tpl'));					
		$mail->setSubject($smarty->fetch('mail/moderate_validation_mail_subject.tpl'));					
		$mail->send(array($email));
		$logslib->add_log('register','validated account '.$user);
	} else {
		$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	}
	$smarty->assign('msg', tra("Account validated successfully."));
	$smarty->display("information.tpl");
} else {
	$smarty->assign('msg', tra("Invalid username or password"));
	$smarty->display("error.tpl");
}

?>
