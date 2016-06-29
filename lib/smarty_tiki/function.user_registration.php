<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_user_registration($params, $smarty)
{
	global $prefs, $https_mode, $base_url_https, $user;
	$registrationlib = TikiLib::lib('registration');
	$userlib = TikiLib::lib('user');

	if ($prefs['allowRegister'] != 'y') {
		return;
	}

	$errorreportlib = TikiLib::lib('errorreport');

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
		TikiLib::lib('access')->redirect($prefs['tikiIndex'], tr('You are logged in'));
		// note that this message might appear also when the user logs in for first time so it has to generic for either case
		return;
	}
	$smarty->assign('showmsg', 'n');
// ensure ssl
	if (!$https_mode && $prefs['https_login'] == 'required') {
		TikiLib::lib('access')->redirect($base_url_https . 'tiki-register.php');
		return;
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
			$errorreportlib->report(tra("Cookies must be enabled to log in to this site"));
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
		//result is empty if fields (including antibot) validate and new user is successfully created
		//no user notification at this stage if user tracker is used
		$result = $registrationlib->register_new_user($_REQUEST);

		if (empty($result)) {
			$_REQUEST['valerror'] = false;
		} else {
			$_REQUEST['valerror'] = $result;
			if (is_array($result)) {
				foreach ($result as $r) {
					$errorreportlib->report($r->msg);
				}
			} elseif (is_a($result, 'RegistrationError')) {
				$errorreportlib->report($result->msg);
			} elseif (is_string($result) && $registrationlib->merged_prefs['userTracker'] !== 'y') {	// more to do for usertrackers
				return $result;
			} elseif (!empty($result['msg']) && $registrationlib->merged_prefs['userTracker'] !== 'y') {
				return $result['msg'];
			}
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
			if (!is_array($re['registrationUsersFieldIds'])) {
				$re['registrationUsersFieldIds'] = explode(':', $re['registrationUsersFieldIds']);
			}
			if ($registrationlib->merged_prefs["user_register_prettytracker"] == 'y' && !empty($registrationlib->merged_prefs["user_register_prettytracker_tpl"])) {
				if (substr($registrationlib->merged_prefs["user_register_prettytracker_tpl"], -4) == ".tpl") {
					$userTrackerData = wikiplugin_tracker('',
						array(
							'trackerId' => $re['usersTrackerId'],
							'fields' => $re['registrationUsersFieldIds'],
							'showdesc' => 'y',
							'showmandatory' => 'y',
							'embedded' => 'n',
							'action' => tra('Register'),
							'registration' => 'y',
							'tpl' => $registrationlib->merged_prefs["user_register_prettytracker_tpl"],
							'userField' => $re['usersFieldId'],
							'outputwiki' => $outputwiki,
							'outputtowiki' => $outputtowiki,
							'chosenGroup' => $chosenGroup,
						));
				} else {
					$userTrackerData = wikiplugin_tracker('',
						array(
							'trackerId' => $re['usersTrackerId'],
							'fields' => $re['registrationUsersFieldIds'],
							'showdesc' => 'y',
							'showmandatory' => 'y',
							'embedded' => 'n',
							'action' => tra('Register'),
							'registration' => 'y',
							'wiki' => $registrationlib->merged_prefs["user_register_prettytracker_tpl"],
							'userField' => $re['usersFieldId'],
							'outputwiki' => $outputwiki,
							'outputtowiki' => $outputtowiki,
							'chosenGroup' => $chosenGroup,
						));
				}
			} else {
				$userTrackerData = wikiplugin_tracker('',
					array(
						'trackerId' => $re['usersTrackerId'],
						'fields' => $re['registrationUsersFieldIds'],
						'showdesc' => 'y',
						'showmandatory' => 'y',
						'embedded' => 'n',
						'action' => tra('Register'),
						'registration' => 'y',
						'userField' => $re['usersFieldId'],
						'chosenGroup' => $chosenGroup,
					)
				);
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
		} elseif (isset($_REQUEST['name']) && !empty($re['usersTrackerId']) && empty($re['registrationUsersFieldIds'])) {
			// If user has been created in the first round and there is a user tracker specified but fields are not set - proceed anyway
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
	}

	if ($needs_validation_js) {
		$registrationlib->addRegistrationFormValidationJs();
	}

	$smarty->assign('email_valid', 'y');
	return $smarty->fetch('user_registration.tpl');
}

