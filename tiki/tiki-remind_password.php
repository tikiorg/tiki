<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-remind_password.php,v 1.27 2006-09-19 16:33:17 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($forgotPass != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": forgotPass");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('showmsg', 'n');
$smarty->assign('showfrm', 'y');

$isvalid = false;
if (isset($_REQUEST["user"])) {
	// this is a 'new password activation':
	if (isset($_REQUEST["actpass"])) {
		$oldPass = $userlib->activate_password($_REQUEST["user"], $_REQUEST["actpass"]);
		if ($oldPass) {
			header ("location: tiki-change_password.php?user=".$_REQUEST["user"].
				"&oldpass=".$oldPass);
			die;
		}
		$smarty->assign('msg', tra("Invalid username or activation code. Maybe this code has already been used."));
		$smarty->display("error.tpl");
		die;
	}
}

if (isset($_REQUEST["remind"])) {
	if (!($ok = $userlib->user_exists($_REQUEST["username"]))) {
		list($cant, $u) = $userlib->other_user_exists_case_insensitive($_REQUEST["username"]);
		if ($cant == 1) {
			$ok = true;
			$_REQUEST["username"] = $u;
		}
	}
	if ($ok) {
		include_once ('lib/webmail/tikimaillib.php');
		$email = $userlib->get_user_email($_REQUEST["username"]);

		if(empty($email)) { //only renew if i can mail the pass
	                $smarty->assign('showmsg', 'e');

       		         $tmp = tra("Unable to send mail. User has not configured email");
               		 $tmp .= ": " . $_REQUEST["username"];
               		 $smarty->assign('msg', $tmp);
		} else {

		if ($feature_clear_passwords == 'y') {
			$pass = $userlib->get_user_password($_REQUEST["username"]);
			$smarty->assign('clearpw', 'y');
		} else {
			$pass = $userlib->renew_user_password($_REQUEST["username"]);
			$smarty->assign('clearpw', 'n');
		}

		$languageEmail = $tikilib->get_user_preference($_REQUEST["username"], "language", $language);
		
		// Now check if the user should be notified by email
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix(). dirname($foo["path"]);
		$machine = preg_replace("!/$!", "", $machine); // just incase
		$smarty->assign('mail_machine', $machine);

		$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
		$smarty->assign('mail_user', $_REQUEST["username"]);
		$smarty->assign('mail_same', $feature_clear_passwords);
		$smarty->assign('mail_pass', $pass);
		$smarty->assign('mail_apass', md5($pass));
		$smarty->assign('mail_ip', $_SERVER['REMOTE_ADDR']);
		$mail_data = sprintf($smarty->fetchLang($languageEmail, 'mail/password_reminder_subject.tpl'),$_SERVER["SERVER_NAME"]);
		$mail = new TikiMail($_REQUEST["username"]);
		$mail->setSubject(sprintf($mail_data, $_SERVER["SERVER_NAME"]));
		$mail->setText($smarty->fetchLang($languageEmail, 'mail/password_reminder.tpl'));
		$mail->send(array($email));

		// Just show "success" message and no form
		$smarty->assign('showmsg', 'y');
		$smarty->assign('showfrm', 'n');

		if ($feature_clear_passwords == 'y') {
			$tmp = tra("A password and your IP address reminder email has been sent ");
		} else {
			$tmp = tra("A new (and temporary) password and your IP address has been sent ");
		}

		$tmp .= tra("to the registered email address for");
		$tmp .= " " . $_REQUEST["username"] . ".";
		$smarty->assign('msg', $tmp);
		}
	} else {
		// Show error message (and leave form visible so user can fix problem)
		$smarty->assign('showmsg', 'e');

		$tmp = tra("Invalid or unknown username");
		$tmp .= ": " . $_REQUEST["username"];
		$smarty->assign('msg', $tmp);
	}
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-remind_password.tpl');
$smarty->display("tiki.tpl");

?>
