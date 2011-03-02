<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/registration/registrationlib.php');
if (is_a($registrationlib->merged_prefs, "RegistrationError")) register_error($registrationlib->merged_prefs->msg);
$smarty->assign_by_ref('merged_prefs', $registrationlib->merged_prefs);

$smarty->assign('headtitle', tra('Register'));
// Permission: needs p_register and not to be a slave
if ($prefs['allowRegister'] != 'y') {
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
//get custom fields
$customfields = $registrationlib->get_customfields();
$smarty->assign_by_ref('customfields', $customfields);

//groups choice
if (count($registrationlib->merged_prefs['choosable_groups'])) {
    $smarty->assign('listgroups', $registrationlib->merged_prefs['choosable_groups']);
    if (count($registrationlib->merged_prefs['choosable_groups']) == 1) {
        $smarty->assign_by_ref('theChoiceGroup', $registrationlib->merged_prefs['choosable_groups'][0]['groupName']);
    }
}

$email_valid='y';
if (isset($_REQUEST['register'])) {
	check_ticket('register');
	$cookie_name = $prefs['session_cookie_name'];

	if( ini_get('session.use_cookie') && ! isset( $_COOKIE[$cookie_name] ) )
	    register_error(tra("You have to enable cookies to be able to login to this site"));

	$smarty->assign('errortype', 'no_redirect_login');

	$result=$registrationlib->register_new_user($_REQUEST);
	if (is_a($result,"RegistrationError")) {
		if ($result->field == 'email' && $result->field == 'email_not_valid') // i'm not sure why email is a special case..
			$email_valid='n';
		else
			register_error($result->msg);
	} else if (is_string($result)) {
		$smarty->assign('msg', $result);
		$smarty->assign('showmsg', 'y');
	} elseif (!empty($result['msg'])) {
		$smarty->assign('msg', $result['msg']);
		$smarty->assign('showmsg', 'y');
	}

}

if ($registrationlib->merged_prefs['userTracker'] == 'y') {
	$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
	if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
		include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
		$user = $_REQUEST['name']; // so that one can set user preferences at registration time
		if ($registrationlib->merged_prefs["user_register_prettytracker"] == 'y' && !empty($registrationlib->merged_prefs["user_register_prettytracker_tpl"])) {
			if (substr($registrationlib->merged_prefs["user_register_prettytracker_tpl"], -4) == ".tpl") {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'tpl' => $registrationlib->merged_prefs["user_register_prettytracker_tpl"]));
			} else {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'wiki' => $registrationlib->merged_prefs["user_register_prettytracker_tpl"]));
			}	
		} else {
			$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y'));
		}
		$tr = TikiLib::lib('trk')->get_tracker($re['usersTrackerId']);
		if (!empty($tr['description'])) {
			$smarty->assign('userTrackerHasDescription', true);
		}
		$user = ''; // reset $user for security reasons
		$smarty->assign('userTrackerData', $userTrackerData);
	}
}

$smarty->assign('email_valid', $email_valid);
ask_ticket('register');

if (isset($redirect) && !empty($redirect)) {
	header('Location: '.$redirect);
	exit;
}

$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");
$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $registrationlib->merged_prefs['min_username_length'], "0-9,a-z,A-Z"));
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $registrationlib->merged_prefs['min_pass_length'], "0-9,a-z,A-Z"));
$smarty->assign('min_username_length', $registrationlib->merged_prefs['min_username_length']);
$smarty->assign('min_pass_length', $registrationlib->merged_prefs['min_pass_length']);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// xajax


//if ($prefs['feature_ajax'] == 'y') {	//AJAX_TODO
//	$ajaxlib->registerFunction('chkRegName');
//	$ajaxlib->registerFunction('chkRegEmail');
//	$ajaxlib->registerTemplate('tiki-register.tpl');
//}

function register_error($msg) {
	global $smarty;
	$smarty->assign('msg', $msg);
	$smarty->assign('errortype', 0);
	$smarty->display("error.tpl");
	die;
}

if (empty($module) || !$module) {
	$smarty->assign('mid', 'tiki-register.tpl');
	$smarty->assign('openid_associate', 'n');
	$smarty->display('tiki.tpl');
}
