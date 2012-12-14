<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_user_registration($params, $smarty)
{
	global $prefs, $userlib, $https_mode, $base_url_https, $registrationlib, $user;

	if ($prefs['allowRegister'] != 'y') {
		return;
	}

	$errorreportlib = TikiLib::lib('errorreport');

	include_once('lib/registration/registrationlib.php');

	$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");
	$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $registrationlib->merged_prefs['min_username_length'], "0-9,a-z,A-Z"));
	$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $registrationlib->merged_prefs['min_pass_length'], "0-9,a-z,A-Z"));
	$smarty->assign('min_username_length', $registrationlib->merged_prefs['min_username_length']);
	$smarty->assign('min_pass_length', $registrationlib->merged_prefs['min_pass_length']);
	if (is_a($registrationlib->merged_prefs, "RegistrationError")) $errorreportlib->report($registrationlib->merged_prefs->msg);
	$smarty->assignByRef('merged_prefs', $registrationlib->merged_prefs);
	$smarty->assign('allowRegister', 'y'); // Used for OpenID associations
	$smarty->assign('openid_associate', 'n');

// NOTE that this is not a standard access check, it checks for the opposite of that, i.e. whether logged in already
	if (!empty($user)) {
		$smarty->assign('msg', tra('You are already logged in'));
		$smarty->display('error.tpl');
		return;
	}
	$smarty->assign('showmsg', 'n');
// ensure ssl
	if (!$https_mode && $prefs['https_login'] == 'required') {
		header('Location: ' . $base_url_https . 'tiki-register.php');
		die();
	}
//get custom fields
	$customfields = $registrationlib->get_customfields();
	$smarty->assignByRef('customfields', $customfields);

//groups choice
	if (count($registrationlib->merged_prefs['choosable_groups'])) {
		$smarty->assign('listgroups', $registrationlib->merged_prefs['choosable_groups']);
		if (count($registrationlib->merged_prefs['choosable_groups']) == 1) {
			$smarty->assignByRef('theChoiceGroup', $registrationlib->merged_prefs['choosable_groups'][0]['groupName']);
		}
		if ($registrationlib->merged_prefs['userTracker'] == 'y') {
			$smarty->assign('trackerEditFormId', 1);	// switch on to make mandatory_star *'s appear even though the tracker form is loaded by ajax
		}
	}

	if (isset($_REQUEST['register']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
		check_ticket('register');
		$cookie_name = $prefs['session_cookie_name'];

		if ( ini_get('session.use_cookie') && ! isset( $_COOKIE[$cookie_name] ) ) {
			$errorreportlib->report(tra("You have to enable cookies to be able to login to this site"));
			return '';
		}

		if ($registrationlib->merged_prefs['http_referer_registration_check'] === 'y') {
			global $base_host;
			if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $base_host) === false) {
				$errorreportlib->report(tra('Request not from this host.'));
				return '';
			}
		}

		$smarty->assign('errortype', 'no_redirect_login');

		$result = $registrationlib->register_new_user($_REQUEST);

		if (is_array($result)) {
			foreach ($result as $r) {
				$errorreportlib->report($r->msg);
			}
		} else if (is_a($result, 'RegistrationError')) {
			$errorreportlib->report($result->msg);
		} else if (is_string($result) && $registrationlib->merged_prefs['userTracker'] !== 'y') {	// more to do for usertrackers
			return $result;
		} elseif (!empty($result['msg']) && $registrationlib->merged_prefs['userTracker'] !== 'y') {
			return $result['msg'];
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
		$chosenGroup = 'Registered';
		if (count($registrationlib->merged_prefs['choosable_groups']) > 0 && isset($_REQUEST['chosenGroup'])) {
			$chosenGroup =  $_REQUEST['chosenGroup'];
			if (!$userlib->group_exists($chosenGroup) || $userlib->get_registrationChoice($chosenGroup) !== 'y') {
				$result = null;						// invalid group chosen
				$smarty->assign('msg', '');
				$smarty->assign('showmsg', 'n');
				$chosenGroup = '';
			}
		}
		$re = $userlib->get_group_info($chosenGroup);
		if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
			$needs_validation_js = false;
			include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
			if (isset($_REQUEST['name'])) {
				$user = $_REQUEST['name'];	// so that one can set user preferences at registration time
				$_REQUEST['iTRACKER'] = 1;	// only one tracker plugin on registration
			}
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
			if (isset($_REQUEST['error']) && $_REQUEST['error'] === 'y') {
				$result = null;
				$smarty->assign('msg', '');
				$smarty->assign('showmsg', 'n');

			} else if (isset($_REQUEST['name'])) {		// user tracker saved ok

				$result = $registrationlib->register_new_user($_REQUEST);
				if (is_array($result)) {
					foreach ($result as $r) {
						$errorreportlib->report($r->msg);
					}
				} else if (is_a($result, 'RegistrationError')) {
					$errorreportlib->report($result->msg);
				} else {
					$user = ''; // reset $user
					return $result;
				}
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
				required: true,';
		if ($prefs['login_is_email'] === 'y') {
			$js .= '
				email: true,';
		}
		$js .= '
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

	$smarty->assign('email_valid', 'y');
	return $smarty->fetch('user_registration.tpl');
}