<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/registration/registrationlib.php');
include_once ('lib/notifications/notificationlib.php');
$smarty->assign('headtitle', tra('Register'));
// Permission: needs p_register and not to be a slave
if ($prefs['allowRegister'] != 'y' || ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']))) {
	header("location: index.php");
	die;
}
$smarty->assign('allowRegister', 'y'); // Used for OpenID associations
// NOTE that this is not a standard access check, it checks for the opposite of that, i.e. whether logged in already
if (!empty($user)) {
	$smarty->assign('msg', tra('You are already logged in'));
	$smarty->display('error.tpl');
	die;
}
$smarty->assign('showmsg', 'n');
// ensure ssl
if (!$https_mode && $prefs['https_login'] == 'required') {
	header('Location: ' . $base_url_https . 'tiki-register.php');
	die;
}
// novalidation is set to yes if a user confirms his email is correct after tiki fails to validate it
if (!isset($_REQUEST['novalidation'])) {
	if (!empty($_REQUEST['trackit'])) $novalidation = 'yes'; // the user has already confirmed manually that SnowCheck is not working
	else $novalidation = '';
} else {
	$novalidation = $_REQUEST['novalidation'];
}
//get custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields();
$smarty->assign_by_ref('customfields', $customfields);
$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
$nbChoiceGroups = 0;
$mandatoryChoiceGroups = true;
foreach($listgroups['data'] as $gr) {
	if ($gr['registrationChoice'] == 'y') {
		++$nbChoiceGroups;
		$theChoiceGroup = $gr['groupName'];
		if ($gr['groupName'] == 'Registered') $mandatoryChoiceGroups = false;
	}
}
if ($nbChoiceGroups) {
	$smarty->assign('listgroups', $listgroups['data']);
	if ($nbChoiceGroups == 1) {
		$smarty->assign_by_ref('theChoiceGroup', $theChoiceGroup);
	}
}
if (isset($_REQUEST['register']) && !empty($_REQUEST['name']) && (isset($_REQUEST['pass']) || isset($_SESSION['openid_url']))) {
	check_ticket('register');
	$cookie_name = $prefs['session_cookie_name'];

	if( ini_get('session.use_cookie') && ! isset( $_COOKIE[$cookie_name] ) ) {
		$smarty->assign('msg',tra("You have to enable cookies to be able to login to this site"));
		$smarty->display("error.tpl");
		exit;
	}

	$smarty->assign('errortype', 'no_redirect_login');
	if ($novalidation != 'yes' and ($_REQUEST["pass"] <> $_REQUEST["passAgain"]) and !isset($_SESSION['openid_url'])) {
		$smarty->assign('msg', tra("The passwords don't match"));
		$smarty->display("error.tpl");
		die;
	}
	if ($userlib->user_exists($_REQUEST["name"])) {
		$smarty->assign('msg', tra("User already exists"));
		$smarty->display("error.tpl");
		die;
	}
	if (!$user && $prefs['rnd_num_reg'] == 'y' && !isset($_SESSION['in_tracker'])) {
		if (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode']) {
			$smarty->assign('msg', tra("You have mistyped the anti-bot verification code; please try again."));
			$smarty->display("error.tpl");
			die;
		}
	}
	// VALIDATE NAME HERE
	$n = strtolower($_REQUEST['name']);
	if ($n == 'admin' || $n == 'anonymous' || $n == 'registered' || $n == strtolower(tra('Anonymous')) || $n == strtolower(tra('Registered'))) {
		$smarty->assign('msg', tra("Invalid username"));
		$smarty->display("error.tpl");
		die;
	}
	if (strlen($_REQUEST["name"]) > 200) {
		$smarty->assign('msg', tra("Username is too long"));
		$smarty->display("error.tpl");
		die;
	}
	if ($prefs['lowercase_username'] == 'y') {
		if (preg_match('/[[:upper:]]/', $_REQUEST["name"])) {
			$smarty->assign('msg', tra("Username cannot contain uppercase letters"));
			$smarty->display("error.tpl");
			die;
		}
	}
	//FALTA DEFINIR VALORES PADRÕES PARA AS DUAS VARIÁVEIS!!!
	if (strlen($_REQUEST["name"]) < $prefs['min_username_length']) {
		$smarty->assign('msg', tra("Username must be at least") . ' ' . $prefs['min_username_length'] . ' ' . tra("characters long"));
		$smarty->display("error.tpl");
		die;
	}
	if (strlen($_REQUEST["name"]) > $prefs['max_username_length']) {
		$smarty->assign('msg', tra("Username cannot contain more than") . ' ' . $prefs['max_username_length'] . ' ' . tra("characters"));
		$smarty->display("error.tpl");
		die;
	}
	$newPass = $_REQUEST["pass"] ? $_REQUEST["pass"] : $_REQUEST["genepass"];
	$polerr = $userlib->check_password_policy($newPass);
	if (!isset($_SESSION['openid_url']) && (strlen($polerr) > 0)) {
		$smarty->assign('msg', $polerr);
		$smarty->display("error.tpl");
		die;
	}
	if (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $_REQUEST["name"])) {
		$smarty->assign('msg', tra("Invalid username"));
		$smarty->display("error.tpl");
		die;
	}
	// Check the mode
	if ($prefs['useRegisterPasscode'] == 'y') {
		if ($_REQUEST['passcode'] != $prefs['registerPasscode']) {
			$smarty->assign('msg', tra("Wrong passcode. You need to know the passcode to register at this site"));
			$smarty->display("error.tpl");
			die;
		}
	}
	if ($nbChoiceGroups > 0 && $mandatoryChoiceGroups && empty($_REQUEST['chosenGroup'])) {
		$smarty->assign('msg', tra('You must choose a group'));
		$smarty->display("error.tpl");
		die;
	}
	if ($prefs['login_is_email'] == 'y') {
		if (empty($_REQUEST['novalidation']) || $_REQUEST['novalidation'] != 'yes') {
			$_POST['email'] = $_REQUEST['email'] = $_REQUEST['name'];
		} else {
			$_POST['name'] = $_REQUEST['name'] = $_REQUEST['email'];
		}
	}
	$email_valid = 'y';
	if (!validate_email($_REQUEST["email"], $prefs['validateEmail'])) {
		$email_valid = 'n';
	} elseif ($prefs['userTracker'] == 'y') {
		$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
		if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
			include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
			$_SESSION['in_tracker'] = true;
			$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n'));
			$smarty->assign('userTrackerData', $userTrackerData);
			if (!isset($_REQUEST['trackit']) || (isset($_REQUEST['error']) && $_REQUEST['error'] == 'y')) {
				$email_valid = 'n'; // first pass or error
				
			}
		}
	}
	if ($email_valid == 'y') {
		if (isset($_SESSION['openid_url'])) {
			$openid_url = $_SESSION['openid_url'];
		} else {
			$openid_url = '';
		}
		if ($prefs['validateUsers'] == 'y' || (isset($prefs['validateRegistration']) && $prefs['validateRegistration'] == 'y')) {
			$apass = addslashes(md5($tikilib->genPass()));
			$userlib->send_validation_email($_REQUEST['name'], $apass, $_REQUEST['email'], '', '', isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : '');
			$userlib->add_user($_REQUEST["name"], $newPass, $_REQUEST["email"], '', false, $apass, $openid_url , $prefs['validateRegistration'] == 'y'?'a':'u');
			$logslib->add_log('register', 'created account ' . $_REQUEST["name"]);
			$smarty->assign('showmsg', 'y');
		} else {
			$userlib->add_user($_REQUEST["name"], $newPass, $_REQUEST["email"], '', false, NULL, $openid_url);
			$logslib->add_log('register', 'created account ' . $_REQUEST["name"]);
			$smarty->assign('msg', $smarty->fetch('mail/user_welcome_msg.tpl'));
			$smarty->assign('showmsg', 'y');
		}
		if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
			$userlib->set_default_group($_REQUEST['name'], $_REQUEST['chosenGroup']);
		} elseif (empty($_REQUEST['chosenGroup']) && isset($_SESSION['in_tracker'])) {
			$userlib->set_default_group($_REQUEST['name'], 'Registered'); // to have tiki-user_preferences links par default to the registration tracker
		}
		$userlib->set_email_group($_REQUEST['name'], $_REQUEST['email']);
		unset($_SESSION['in_tracker']);
		// save default user preferences
		$tikilib->set_user_preference($_REQUEST['name'], 'theme', $prefs['style']);
		$tikilib->set_user_preference($_REQUEST['name'], 'userbreadCrumb', $prefs['users_prefs_userbreadCrumb']);
		$tikilib->set_user_preference($_REQUEST['name'], 'language', $prefs['users_prefs_language']);
		$tikilib->set_user_preference($_REQUEST['name'], 'display_timezone', $prefs['users_prefs_display_timezone']);
		$tikilib->set_user_preference($_REQUEST['name'], 'user_information', $prefs['users_prefs_user_information']);
		$tikilib->set_user_preference($_REQUEST['name'], 'user_dbl', $prefs['users_prefs_user_dbl']);
		$tikilib->set_user_preference($_REQUEST['name'], 'diff_versions', $prefs['users_prefs_diff_versions']);
		$tikilib->set_user_preference($_REQUEST['name'], 'show_mouseover_user_info', $prefs['users_prefs_show_mouseover_user_info']);
		$tikilib->set_user_preference($_REQUEST['name'], 'email is public', $prefs['users_prefs_email_is_public']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mailCharset', $prefs['users_prefs_mailCharset']);
		$tikilib->set_user_preference($_REQUEST['name'], 'realName', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'homePage', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'lat', floatval(0));
		$tikilib->set_user_preference($_REQUEST['name'], 'lon', floatval(0));
		$tikilib->set_user_preference($_REQUEST['name'], 'country', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_maxRecords', $prefs['users_prefs_mess_maxRecords']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_archiveAfter', $prefs['users_prefs_mess_archiveAfter']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_sendReadStatus', $prefs['users_prefs_mess_sendReadStatus']);
		$tikilib->set_user_preference($_REQUEST['name'], 'minPrio', $prefs['users_prefs_minPrio']);
		$tikilib->set_user_preference($_REQUEST['name'], 'allowMsgs', $prefs['users_prefs_allowMsgs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_pages', $prefs['users_prefs_mytiki_pages']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_blogs', $prefs['users_prefs_mytiki_blogs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_articles', $prefs['users_prefs_mytiki_articles']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_gals', $prefs['users_prefs_mytiki_gals']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_msgs', $prefs['users_prefs_mytiki_msgs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_tasks', $prefs['users_prefs_mytiki_tasks']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_items', $prefs['users_prefs_mytiki_items']);
		$tikilib->set_user_preference($_REQUEST['name'], 'tasks_maxRecords', $prefs['users_prefs_tasks_maxRecords']);
		// Custom fields
		foreach($customfields as $custpref => $prefvalue) {
			if (isset($_REQUEST[$customfields[$custpref]['prefName']])) $tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
		}
		$emails = $notificationlib->get_mail_events('user_registers', '*');
		if (count($emails)) {
			include_once ("lib/notifications/notificationemaillib.php");
			$smarty->assign('mail_user', $_REQUEST["name"]);
			$smarty->assign('mail_date', $tikilib->now);
			$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
			sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
		}
	}
}
$smarty->assign('email_valid', $email_valid);
ask_ticket('register');
$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");
$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $prefs['min_username_length'], "0-9,a-z,A-Z"));
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $prefs['min_pass_length'], "0-9,a-z,A-Z"));
$smarty->assign('min_username_length', $prefs['min_username_length']);
$smarty->assign('min_pass_length', $prefs['min_pass_length']);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// xajax


if ($prefs['feature_ajax'] == 'y') {
	global $ajaxlib;
	include_once ('lib/ajax/ajaxlib.php');
//	include_once ('tiki-regsiter_ajax.php');
	$ajaxlib->registerFunction('chkRegName');
	$ajaxlib->registerFunction('chkRegEmail');
	$ajaxlib->registerTemplate('tiki-register.tpl');
	$ajaxlib->processRequests();
}


function chkRegName($name) {
	global $smarty, $ajaxlib, $userlib;
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
	$ajaxlib->registerTemplate('tiki-register.tpl');
	$objResponse = new xajaxResponse();
	if ( empty($name) ) {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("Missing User Name"));
	} elseif ( $userlib->user_exists($name) ) {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("User Already Exists"));
	} else {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_yes.tra("Valid User Name"));
	}
	return $objResponse;
}

function chkRegEmail($mail) {
	global $smarty, $ajaxlib;
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
	$ajaxlib->registerTemplate('tiki-register.tpl');
	$objResponse = new xajaxResponse();
	if (empty($mail)) {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra("Missing Email"));
	} elseif (!preg_match('/^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$/i', $mail)) {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra('This is not a valid mail adress'));
	} else {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_yes.tra("Valid Email"));
	}
	return $objResponse;
}

if (empty($module) || !$module) {
	$smarty->assign('mid', 'tiki-register.tpl');
	$smarty->assign('openid_associate', 'n');
	$smarty->display('tiki.tpl');
}
