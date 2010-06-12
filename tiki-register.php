<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/registration/registrationlib.php');

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
	$novalidation = '';
} else {
	$novalidation = $_REQUEST['novalidation'];
}
//get custom fields
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

if (isset($_REQUEST['register'])) {
	check_ticket('register');
	$cookie_name = $prefs['session_cookie_name'];

	if( ini_get('session.use_cookie') && ! isset( $_COOKIE[$cookie_name] ) )
	    register_error(tra("You have to enable cookies to be able to login to this site"));

	$smarty->assign('errortype', 'no_redirect_login');

	$email_valid='y';
	$result=$registrationlib->register_new_user($_REQUEST);
	if (is_a($result, 'RegistrationError')) {
		if ($result->field == 'email' && $result->field == 'email_not_valid') // i'm not sure why email is a special case..
			$email_valid='n';
		else
			register_error($result->msg);
	} else if (is_string($result)) {
		$smarty->assign('msg', $result);
		$smarty->assign('showmsg', 'y');
	}

}

if ($prefs['userTracker'] == 'y') {
	$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
	if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
		include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
		if ($prefs["user_register_prettytracker"] == 'y' && !empty($prefs["user_register_prettytracker_tpl"])) {
			if (substr($prefs["user_register_prettytracker_tpl"], -4) == ".tpl") {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'tpl' => $prefs["user_register_prettytracker_tpl"]));
			} else {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'wiki' => $prefs["user_register_prettytracker_tpl"]));
			}	
		} else {
			$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y'));
		}
		$smarty->assign('userTrackerData', $userTrackerData);
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

function register_error($msg) {
	global $smarty;
	$smarty->assign('msg', $msg);
	$smarty->display("error.tpl");
	die;
}

if (empty($module) || !$module) {
	$smarty->assign('mid', 'tiki-register.tpl');
	$smarty->assign('openid_associate', 'n');
	$smarty->display('tiki.tpl');
}
