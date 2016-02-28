<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'user' => 'text',
	) )
);

require_once ('tiki-setup.php');
$access->check_feature(array('validateUsers','validateRegistration'), '', 'login', true);
$isvalid = false;
if (isset($_REQUEST["user"])) {
	if (isset($_REQUEST["pass"])) {
		if (empty($_REQUEST['pass']) && $tiki_p_admin_users === 'y') {// case: user invalidated his account with wrong password- no email was sent - admin must reactivate
			$userlib->change_user_waiting($_REQUEST['user'], NULL);
			$userlib->set_unsuccessful_logins($_REQUEST['user'], 0);
			$smarty->assign('msg', tra("Account validated successfully."));
			$smarty->assign('mid', 'tiki-information.tpl');
			$smarty->display("tiki.tpl");
			die;
		} elseif (!empty($_SESSION['last_validation'])) {
			if ($_SESSION['last_validation']['actpass'] == $_REQUEST["pass"] && $_SESSION['last_validation']['user'] == $_REQUEST["user"]) {
				list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_SESSION['last_validation']['actpass'], '', '', true);
			} else {
				$_SESSION['last_validation'] = null;
			}
		}
		if (!$isvalid) {
			list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_REQUEST["pass"], '', '', true);
			$_SESSION['last_validation'] = $isvalid ? array('user' => $_REQUEST["user"], 'actpass' => $_REQUEST["pass"]) : null;
		}
	} else {
		$error = PASSWORD_INCORRECT;
	}
} else {
	$error = USER_NOT_FOUND;
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$userAutoLoggedIn = FALSE;
if ($isvalid) {
	$wasAdminValidation = false;
	$info = $userlib->get_user_info($_REQUEST['user']);
	if ($info['waiting'] == 'a' && $prefs['validateUsers'] == 'y') { // admin validating -> need user email validation now
		$userlib->send_validation_email($_REQUEST['user'], $info['valid'], $info['email'], '', 'y');
		$userlib->change_user_waiting($_REQUEST['user'], 'u');
		$wasAdminValidation = true;
		$logslib->add_log('register', 'admin validation ' . $_REQUEST['user']);
	} elseif ($info['waiting'] == 'a' && $prefs['validateRegistration'] == 'y') { //admin validating -> user can log in
		$userlib->confirm_user($_REQUEST['user']);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$foo1 = str_replace('tiki-login_validate', 'tiki-login_scr', $foo['path']);
		$machine = $tikilib->httpPrefix(true) . $foo1;
		$smarty->assign('mail_machine', $machine);
		$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
		$smarty->assign('mail_user', $_REQUEST['user']);
		$email = $userlib->get_user_email($_REQUEST['user']);
		include_once ("lib/webmail/tikimaillib.php");
		$mail = new TikiMail();
		$mail->setText($smarty->fetch('mail/moderate_activation_mail.tpl'));
		$mail->setSubject($smarty->fetch('mail/moderate_activation_mail_subject.tpl'));
		$mail->send(array($email));
		$logslib->add_log('register', 'validated account ' . $_REQUEST['user']);
	} elseif (empty($user)) {
		$userlib->confirm_user($_REQUEST['user']);
		if ($info['pass_confirm'] == 0) {
			if (!empty($info['provpass'])) {
				$_SESSION['last_validation']['pass'] = $info['provpass'];
			}
			if (!empty($_SESSION['last_validation']['pass'])) {
				$smarty->assign('oldpass', $_SESSION['last_validation']['pass']);
			}
			$smarty->assign('new_user_validation', 'y');
			$smarty->assign('userlogin', $_REQUEST['user']);
			if ($prefs['login_is_email'] === 'y') {
				$smarty->assign('email', $_REQUEST['user']);
			} else {
				$smarty->assign('email', $info['email']);
			}
			$smarty->assign('mid', 'tiki-change_password.tpl');
			$smarty->display("tiki.tpl");
			die;
		} else {
			$user = $_REQUEST['user'];
			$userAutoLoggedIn = TRUE;
			$_SESSION["$user_cookie_site"] = $user;
			TikiLib::lib('menu')->empty_menu_cache();
		}
	}

	if ($language = $tikilib->get_user_preference($user, 'language')) {
		setLanguage($language);
	}

	if (!empty($prefs['url_after_validation']) && !$wasAdminValidation) {
		$target = $prefs['url_after_validation'];
		$access->redirect($target);
	} elseif ($userAutoLoggedIn == TRUE) {
		$access->redirect($prefs['tikiIndex'], tra("Account validated successfully."));
	} else {
		$smarty->assign('msg', tra("Account validated successfully."));
		$smarty->assign('mid', 'tiki-information.tpl');
		$smarty->display("tiki.tpl");
		die;
	}
} else {
	if ($error == PASSWORD_INCORRECT) $error = tra("Invalid username or password");
	else if ($error == USER_NOT_FOUND) $error = tra("Invalid username or password");
	else if ($error == ACCOUNT_DISABLED) $error = tra("Account requires administrator approval");
	else if ($error == USER_AMBIGOUS) $error = tra("You must use the right case for your username");
	else if ($error == USER_PREVIOUSLY_VALIDATED) $error = tra('You have already validated your account. Please log in.');
	else if ($error == EMAIL_AMBIGUOUS) $error = tra("There is more than one user account with this email. Please contact the administrator.");
	else $error = tra('Invalid username or password');
	$smarty->assign('errortype', 'no_redirect_login');
	$smarty->assign('msg', $error);
	$smarty->display("error.tpl");
}
