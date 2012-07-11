<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'email' => 'email',
		'name' => 'text',
		'pass' => 'text',
		'passAgain' => 'text', 
	) )
);

$auto_query_args = array();

require_once ('tiki-setup.php');
require_once ('lib/registration/registrationlib.php');
if (is_a($registrationlib->merged_prefs, "RegistrationError")) register_error($registrationlib->merged_prefs->msg);
$smarty->assign_by_ref('merged_prefs', $registrationlib->merged_prefs);

// Permission: needs p_register and not to be a slave
if ($prefs['allowRegister'] != 'y') {
	header("location: index.php");
	die;
}
$smarty->assign('allowRegister', 'y'); // Used for OpenID associations
$smarty->assign('openid_associate', 'n');

if (empty($reg_in_module)) {	// set in mod-func-register.php module
	$reg_in_module = false;
	$smarty->assign('headtitle', tra('Register'));
}
$smarty->assign_by_ref('reg_in_module', $reg_in_module);

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
if (isset($_REQUEST['register']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	check_ticket('register');
	$cookie_name = $prefs['session_cookie_name'];

	if ( ini_get('session.use_cookie') && ! isset( $_COOKIE[$cookie_name] ) )
	    register_error(tra("You have to enable cookies to be able to login to this site"));

	$smarty->assign('errortype', 'no_redirect_login');

	$result=$registrationlib->register_new_user($_REQUEST);
	if (is_a($result, "RegistrationError")) {
		if ($result->field == 'email') // i'm not sure why email is a special case..
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
$outputtowiki='';
$outputwiki='';
if ($prefs['user_register_prettytracker_output'] == 'y') {
	$outputtowiki=$prefs["user_register_prettytracker_outputtowiki"];	
	$outputwiki=$prefs["user_register_prettytracker_outputwiki"];
}

$needs_validation_js = true;
if ($registrationlib->merged_prefs['userTracker'] == 'y') {
	$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
	if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
		$needs_validation_js = false;
		include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
		if (isset($_REQUEST['name'])) $user = $_REQUEST['name']; // so that one can set user preferences at registration time
		if ($registrationlib->merged_prefs["user_register_prettytracker"] == 'y' && !empty($registrationlib->merged_prefs["user_register_prettytracker_tpl"])) {
			if (substr($registrationlib->merged_prefs["user_register_prettytracker_tpl"], -4) == ".tpl") {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'tpl' => $re["user_register_prettytracker_tpl"], 'userField' => $re['usersFieldId'], 'outputwiki' => $outputwiki, 'outputtowiki' => $outputtowiki));
			} else {
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'wiki' => $re["user_register_prettytracker_tpl"], 'userField' => $re['usersFieldId'],'outputwiki' => $outputwiki, 'outputtowiki' => $outputtowiki));
			}	
		} else {
			$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $re['registrationUsersFieldIds'], 'showdesc' => 'y', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => tra('Register'), 'registration' => 'y', 'userField' => $re['usersFieldId']));
		}
		$tr = TikiLib::lib('trk')->get_tracker($re['usersTrackerId']);
		if (!empty($tr['description'])) {
			$smarty->assign('userTrackerHasDescription', true);
		}
		if ($_REQUEST['error'] === 'y') {
			$result = null;
			$smarty->assign('msg', '');
			$smarty->assign('showmsg', 'n');
		}
		$user = ''; // reset $user for security reasons
		$smarty->assign('userTrackerData', $userTrackerData);
	}
}

if ($needs_validation_js && $prefs['feature_jquery_validation'] === 'y') {
	$js_m = '';
	$js = '
	$("form[name=RegForm]").validate({
		rules: {
			name: {
				required: true,
				remote: {
					url: "validate-ajax.php",
					type: "post",
					data: {
						validator: "username",
						input: function() { return $("#name").val(); }
					}
				}
			},
			email: {
				required: true,
				email: true
			},
			pass: {
				required: true,
				remote: {
					url: "validate-ajax.php",
					type: "post",
					data: {
						validator: "password",
						input: function() { return $("#pass1").val(); }
					}
				}
			},
			passAgain: { equalTo: "#pass1" }';

	if ($prefs['user_must_choose_group'] === 'y') {
		$choosable_groups = $registrationlib->merged_prefs['choosable_groups'];
		$js .= ',
			chosenGroup: {
				required: true
			}';
		$js_m .= ' "chosenGroup": { required: "' . tra('One of these groups is required') . '"}, ';
	}


	if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox') && $prefs['feature_antibot'] == 'y' && empty($user) && $prefs['recaptcha_enabled'] != 'y') {
		// antibot validation
		$js .= ',
	"captcha[input]": {
		required: true,
		remote: {
			url: "validate-ajax.php",
			type: "post",
			data: {
				validator: "captcha",
				parameter: function() { return $jq("#captchaId").val(); },
				input: function() { return $jq("#antibotcode").val(); }
			}
		}
	}
';
		$js_m .= ' "captcha[input]": { required: "' . tra('This field is required') . '"}, ';
	}

		$js .= '},
		messages: {' . $js_m . '
			name: { required: "This field is required"},
			email: { email: "Invalid email", required: "This field is required"},
			pass: { required: "This field is required"},
			passAgain: { equalTo: "Passwords do not match"}
		},
		submitHandler: function(){process_submit(this.currentForm);}
	});
';
	TikiLib::lib('header')->add_jq_onready($js);
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

function register_error($msg)
{
	TikiLib::lib('errorreport')->report($msg);
}

if (!$reg_in_module) {
	$smarty->assign('mid', 'tiki-register.tpl');
	$smarty->display('tiki.tpl');
}
