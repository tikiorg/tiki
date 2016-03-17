<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$tikifeedback = array();
$errors = array();

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
				'offset' => 'digits',
				'numrows' => 'digits',
				'find' => 'text',
				'filterEmail' => 'xss',
				'sort_mode' => 'text',
				'initial' => 'text',
				'filterGroup' => 'text',
			)
		)
);


require_once ('tiki-setup.php');
// temporary patch: tiki_p_admin includes tiki_p_admin_users but if you don't
// clean the temp/cache each time you sqlupgrade the perms setting is not
// synchronous with the cache
$access = TikiLib::lib('access');
$access->check_permission(array('tiki_p_admin_users'));

if ($tiki_p_admin != 'y') {
	$userGroups = $userlib->get_user_groups_inclusion($user);
	$smarty->assign_by_ref('userGroups', $userGroups);
} else {
	$userGroups = array();
}

/**
 * @param $u
 * @param $reason
 * @return mixed
 */
function discardUser($u, $reason)
{
	$u['reason'] = $reason;
	return $u;
}

function batchImportUsers()
{
	global $tiki_p_admin, $prefs, $userGroups;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$logslib = TikiLib::lib('logs');

	$fname = $_FILES['csvlist']['tmp_name'];
	$fhandle = fopen($fname, 'r');
	$fields = fgetcsv($fhandle, 1000);

	if (!$fields[0]) {
		$smarty->assign('msg', tra('The file has incorrect syntax or is not a CSV file'));
		$smarty->display('error.tpl');
		die;
	}

	if (!in_array('login', $fields) || !in_array('email', $fields) || !in_array('password', $fields)) {
		$smarty->assign('msg', tra('The file does not have the required header:') . ' login, password, email');
		$smarty->display('error.tpl');
		die;
	}

	while (!feof($fhandle)) {
		$data = fgetcsv($fhandle, 1000);
		if (empty($data)) continue;
		$temp_max = count($fields);
		for ($i = 0; $i < $temp_max; $i++) {
			if ($fields[$i] == 'login'
					&& function_exists('mb_detect_encoding')
					&& mb_detect_encoding($data[$i], 'ASCII, UTF-8, ISO-8859-1') == 'ISO-8859-1'
			) {
				$data[$i] = utf8_encode($data[$i]);
			}
			@$ar[$fields[$i]] = $data[$i];
		}
		$userrecs[] = $ar;
	}
	fclose($fhandle);

	if (empty($userrecs) or !is_array($userrecs)) {
		$smarty->assign('msg', tra('No records were found. Check the file please!'));
		$smarty->display('error.tpl');
		die;
	}
	// whether to force password change on first login or not
	$pass_first_login = (isset($_REQUEST['forcePasswordChange']) && $_REQUEST['forcePasswordChange'] == 'on');

	$added = 0;
	$errors = array();
	$discarded = array();

	foreach ($userrecs as $u) {
		$local = array();
		$exist = false;

		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			if (empty($u['login']) && empty($u['email'])) {
				$local[] = discardUser($u, tra('User login or email is required'));
			} else { // pick up the info on the master

				$info = $userlib->interGetUserInfo(
					$prefs['interlist'][$prefs['feature_intertiki_mymaster']],
					empty($u['login']) ? '' : $u['login'],
					empty($u['email']) ? '' : $u['email']
				);

				if (empty($info)) {
					$local[] = discardUser($u, tra('User does not exist on master'));
				} else {
					$u['login'] = $info['login'];
					$u['email'] = $info['email'];
				}
			}
		} else {
			if (empty($u['login'])) {
				$local[] = discardUser($u, tra('User login is required'));
			}

			if (empty($u['password'])) {
				if (!empty($_REQUEST['notification'])) {
					$u['password'] = $tikilib->genPass();
				} else {
					$local[] = discardUser($u, tra('Password is required'));
				}
			}
			if (empty($u['email'])) {
				$local[] = discardUser($u, tra('Email is required'));
			}
		}

		if (!empty($local)) {
			$discarded = array_merge($discarded, $local);
			continue;
		}

		if ($userlib->user_exists($u['login'])) { // exist on local
			$exist = true;
		}

		if ($exist && $_REQUEST['overwrite'] == 'n') {
			$discarded[] = discardUser($u, tra('User is duplicated'));
			continue;
		}

		if (!$exist) {
			if (!empty($_REQUEST['notification'])) {
				$apass = md5($tikilib->genPass());
			} else {
				$apass = '';
			}

			$u['login'] = $userlib->add_user(
				$u['login'],
				$u['password'],
				$u['email'],
				$pass_first_login ? $u['password'] : '',
				$pass_first_login,
				$apass,
				NULL,
				(!empty($_REQUEST['notification']) ? 'u' : NULL)
			);

			global $user;
			$logslib->add_log('adminusers', sprintf(tra('Created account %s <%s>'), $u['login'], $u['email']), $user);
			if (!empty($_REQUEST['notification'])) {
				$realpass = $pass_first_login ? '' : $u['password'];
				$userlib->send_validation_email($u['login'], $apass, $u['email'], '', '', '', 'user_creation_validation_mail', $realpass);
			}
		}

		$userlib->set_user_fields($u);
		if ($exist && isset($_REQUEST['overwriteGroup'])) {
			$userlib->remove_user_from_all_groups($u['login']);
		}

		if (!empty($u['groups'])) {
			$grps = preg_split('/(?<!,),(?!,)/', $u['groups']);
			foreach ($grps as $grp) {
				$grp = preg_replace('/,,/', ',', preg_replace('/^ *(.*) *$/u', "$1", $grp));
				$existg = false;
				if ($userlib->group_exists($grp)) {
					$existg = true;
				} elseif (!empty($_REQUEST['createGroup']) && $userlib->add_group($grp)) {
					$existg = true;
				}

				if (!$existg) {
					$err = tra('Unknown') . ": $grp";
					if (!in_array($err, $errors)) $errors[] = $err;
				} elseif ($tiki_p_admin != 'y' && !array_key_exists($grp, $userGroups)) {
					$smarty->assign('errortype', 401);
					$err = tra('Permission denied') . ": $grp";
					if (!in_array($err, $errors)) $errors[] = $err;
				} else {
					$userlib->assign_user_to_group($u['login'], $grp);
					$logslib->add_log('perms', sprintf(tra('Assigned %s in group %s'), $u['login'], $grp), $user);
				}
			}
		}

		if (!empty($u['default_group'])) {
			$userlib->set_default_group($u['login'], $u['default_group']);
		}

		if (!empty($u['realName'])) {
			$tikilib->set_user_preference($u['login'], 'realName', $u['realName']);
		}
		$added++;
	}
	$smarty->assign('added', $added);

	if (count($discarded)) {
		$smarty->assign('discarded', count($discarded));
		$smarty->assign_by_ref('discardlist', $discarded);
	}

	if (count($errors)) {
		array_unique($errors);
		$smarty->assign_by_ref('batcherrors', $errors);
	}
}

$auto_query_args = array(
	'offset',
	'numrows',
	'find',
	'filterEmail',
	'sort_mode',
	'initial',
	'filterGroup'
);

if (isset($_REQUEST['batch']) && is_uploaded_file($_FILES['csvlist']['tmp_name'])) {
	$access->check_ticket();
	batchImportUsers();
	// Process the form to add a user here

} elseif (isset($_REQUEST['newuser'])) {
	$AddUser= true;;
	$access->check_authenticity(tra('Are you sure you want to add this new user?'));
	// if email validation set check if email addr is set
	if ($prefs['login_is_email'] != 'y' && isset($_REQUEST['need_email_validation']) &&
		 empty($_REQUEST['email'])) {
		$errors[] = array(
			'num' => 1,
			'mes' => tra('Email validation requested but email address not set')
		);
		$AddUser=false;
	}
	if ($_REQUEST['pass'] != $_REQUEST['passAgain']) {
		$errors[] = array(
			'num' => 1,
			'mes' => tra('The passwords do not match')
		);
		$AddUser=false;
	} elseif (empty($_REQUEST['pass']) && empty($_REQUEST['genepass'])) {
		$errors[] = array(
			'num' => 1,
			'mes' => tra('Password not set')
		);
		$AddUser=false;
	}

	$newPass = $_POST['pass'] ? $_POST['pass'] : $_POST['genepass'];
	// Check if the user already exists

	if ($userlib->user_exists($_REQUEST['login'])) {
		$errors[] = array(
			'num' => 1,
			'mes' => sprintf(tra('User %s already exists'), $_REQUEST['login'])
		);
		$AddUser=false;
	}
	if ($prefs['login_is_email'] == 'y' && !validate_email($_REQUEST['login'])) {
		$errors[] = array(
			'num' => 1,
			'mes' => tra('Invalid email') . ' ' . $_REQUEST['login']
		);
		$AddUser=false;
	}
	if (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $_REQUEST['login'])) {
		$errors[] = array(
			'num' => 1,
			'mes' => tra('User login contains invalid characters')
		);
		$AddUser = false;
	}
	// end verify newuser info
	if ($AddUser) {
		$pass_first_login = (isset($_REQUEST['pass_first_login']) && $_REQUEST['pass_first_login'] == 'on');
		$polerr = $userlib->check_password_policy($newPass);
			if (strlen($polerr) > 0) {
				$smarty->assign('msg', $polerr);
				$smarty->display('error.tpl');
				die;
			}

			if ($prefs['login_is_email'] == 'y' and empty($_REQUEST['email']))
				$_REQUEST['email'] = $_REQUEST['login'];

			$send_validation_email = false;

			if (isset($_REQUEST['need_email_validation']) && $_REQUEST['need_email_validation'] == 'on') {
				$send_validation_email = true;
				$apass = md5($tikilib->genPass());
			} else {
				$apass = '';
			}

			if ($_REQUEST['login'] = $userlib->add_user(
				$_REQUEST['login'],
				$newPass,
				$_REQUEST['email'],
				$pass_first_login ? $newPass : '',
				$pass_first_login,
				$apass,
				NULL,
				($send_validation_email ? 'u' : NULL)
			)) {
				$tikifeedback[] = array(
					'num' => 0,
					'mes' => sprintf(tra('New user created with username %s.'), $_REQUEST['login'])
				);

				if ($send_validation_email) {
					// No need to send credentials in mail if the user is forced to choose a new password after validation
					$realpass = $pass_first_login ? '' : $newPass;
					$userlib->send_validation_email(
						$_REQUEST['login'],
						$apass,
						$_REQUEST['email'],
						'',
						'',
						'',
						'user_creation_validation_mail',
						$realpass
					);
				}

				if ($prefs['userTracker'] === 'y' && !empty($_REQUEST['insert_user_tracker_item'])) {
					TikiLib::lib('header')->add_jq_onready('setTimeout(function () { $(".insert-usertracker").click(); });');
					$_REQUEST['user'] = $userlib->get_user_id($_REQUEST['login']);
					$cookietab = '2';
				} else {
					$cookietab = '1';
					$_REQUEST['find'] = $_REQUEST['login'];
				}
			} else {
				$errors[] = array(
					'num' => 1,
					'mes' => sprintf(tra('Impossible to create new %s with %s %s.'), tra('user'), tra('username'), $_REQUEST['login'])
				);
			}
	}

	if (isset($tikifeedback[0]['mes'])) {
		$logslib->add_log('adminusers', $tikifeedback[0]['mes'], $user);
	}

	$cookietab = 1;

} elseif (isset($_REQUEST['action'])) {

	if ($_REQUEST['action'] == 'email_due' && isset($_REQUEST['user'])) {
		$access->check_authenticity(tra('Are you sure you want to reset email due for this user?'));
		$userlib->reset_email_due($_REQUEST['user']);
	}

	if ($_REQUEST['action'] == 'remove_openid' && isset($_REQUEST['userId'])) {
		$access->check_authenticity(tra('Are you sure you want to remove the link with OpenID for this user?'));
		$userlib->remove_openid_link($_REQUEST['userId']);
	}

	$_REQUEST['user'] = '';

	if (isset($tikifeedback[0]['mes'])) {
		$logslib->add_log('adminusers', $tikifeedback[0]['mes'], $user);
	}
}

if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = 'login_asc';
} else {
	$sort_mode = $_REQUEST['sort_mode'];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST['numrows'])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST['numrows'];
}
$smarty->assign_by_ref('numrows', $numrows);

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
} else {
	$offset = $_REQUEST['offset'];
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST['initial'])) {
	$initial = $_REQUEST['initial'];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);

if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if (isset($_REQUEST['filterGroup'])) {
	$filterGroup = $_REQUEST['filterGroup'];
} else {
	$filterGroup = '';
}
$smarty->assign('filterGroup', $filterGroup);

if (isset($_REQUEST['filterEmail'])) {
	$filterEmail = $_REQUEST['filterEmail'];
} else {
	$filterEmail = '';
}
$smarty->assign('filterEmail', $filterEmail);

list($username, $usermail, $usersTrackerId, $chlogin) = array('', '', '',	false);

if (isset($_REQUEST['user']) and $_REQUEST['user']) {
	if (!is_numeric($_REQUEST['user'])) {
		$_REQUEST['user'] = $userlib->get_user_id($_REQUEST['user']);
	}
	$userinfo = $userlib->get_userid_info($_REQUEST["user"]);
	$cookietab = '2';

	// If login is e-mail, email field needs to be the same as name (and is generally not send)
	if ($prefs['login_is_email'] == 'y' && isset($_POST['login']))
		$_POST['email'] = $_POST['login'];

	if (isset($_POST['edituser']) and isset($_POST['login']) and isset($_POST['email'])) {
		$access->check_authenticity(tra("Are you sure you want to modify this user's data?"));

		if (!empty($_POST['login'])) {
			if ($userinfo['login'] != $_POST['login'] && $userinfo['login'] != 'admin') {
				if ($userlib->user_exists($_POST['login'])) {
					$errors[] = array(
						'num' => 1,
						'mes' => tra('User already exists')
					);
				} elseif (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $_POST['login'])) {
					$errors[] = array(
						'num' => 1,
						'mes' => tra('Login contains invalid characters')
					);
				} elseif ($userlib->change_login($userinfo['login'], $_POST['login'])) {
					$tikifeedback[] = array(
						'num' => 0,
						'mes' => sprintf(tra('%s changed from %s to %s'), tra('login'), $userinfo['login'], $_POST['login'])
					);

					$logslib->add_log(
						'adminusers',
						'changed login for ' . $_POST['login'] . ' from ' . $userinfo['login'] . ' to ' . $_POST['login'],
						$user
					);

					$userinfo['login'] = $_POST['login'];
				} else {
					$errors[] = array(
						'num' => 1,
						'mes' => sprintf(tra("Impossible to change %s from %s to %s"), tra('login'), $userinfo['login'], $_POST['login'])
					);
				}
			}
		}

		$pass_first_login = (isset($_REQUEST['pass_first_login']) && $_REQUEST['pass_first_login'] == 'on');
		if ((isset($_POST['pass']) && $_POST["pass"]) || $pass_first_login || (isset($_POST['genepass']) && $_POST['genepass'])) {
			if ($_POST['pass'] != $_POST['passAgain']) {
				$smarty->assign('msg', tra('The passwords do not match'));
				$smarty->display('error.tpl');
				die;
			}

			if ($tiki_p_admin == 'y' || $tiki_p_admin_users == 'y' || $userinfo['login'] == $user) {
				$newPass = $_POST['pass'] ? $_POST['pass'] : $_POST['genepass'];
				$polerr = $userlib->check_password_policy($newPass);
				if (strlen($polerr) > 0 && !$pass_first_login) {
					$smarty->assign('msg', $polerr);
					$smarty->display('error.tpl');
					die;
				}

				if ($userlib->change_user_password($userinfo['login'], $newPass, $pass_first_login)) {
					$tikifeedback[] = array(
						'num' => 0,
						'mes' => sprintf(tra('%s modified successfully.'), tra('password'))
					);
					$logslib->add_log('adminusers', 'changed password for ' . $_POST['login'], $user);
				} else {
					$errors[] = array(
						'num' => 0,
						'mes' => sprintf(tra('%s modification failed.'), tra('password'))
					);
				}
			}
		}

		if ($userinfo['email'] != $_POST['email']) {
			if ($userlib->change_user_email($userinfo['login'], $_POST['email'], '')) {
				if ($prefs['login_is_email'] != 'y') {
					$tikifeedback[] = array(
						'num' => 0,
						'mes' => sprintf(tra('%s changed from %s to %s'), tra('email'), $userinfo['email'], $_POST['email'])
					);
					$logslib->add_log('adminusers', 'changed email for' . $_POST['login'] . ' from ' . $userinfo['email'] . ' to ' . $_POST['email'], $user);
				}
				$userinfo['email'] = $_POST['email'];
			} else {
				$errors[] = array(
					'num' => 1,
					'mes' => sprintf(tra('Impossible to change %s from %s to %s'), tra('email'), $userinfo['email'], $_POST['email'])
				);
			}
		}
		// check need_email_validation
		if (!empty($_POST['login']) && !empty($_POST['email']) && !empty($_POST['need_email_validation'])) {
			$userlib->invalidate_account($_POST['login']);
			$userinfo = $userlib->get_user_info($_POST['login']);
			$userlib->send_validation_email($_POST['login'], $userinfo['valid'], $_POST['email'], 'y');
		}

		$cookietab = '1';
	}

	if ($prefs['userTracker'] == 'y') {
		$re = $userlib->get_usertracker($_REQUEST['user']);
		if ($re['usersTrackerId']) {
			$trklib = TikiLib::lib('trk');
			$userstrackerid = $re['usersTrackerId'];
			$smarty->assign('userstrackerid', $userstrackerid);
			$usersFields = $trklib->list_tracker_fields($usersTrackerId, 0, -1, 'position_asc', '');
			$smarty->assign_by_ref('usersFields', $usersFields['data']);
			if (isset($re['usersFieldId']) and $re['usersFieldId']) {
				$usersfieldid = $re['usersFieldId'];
				$smarty->assign('usersfieldid', $usersfieldid);

				$usersitemid = $trklib->get_item_id($userstrackerid, $usersfieldid, $re['user']);
				$smarty->assign('usersitemid', $usersitemid);

				if (empty($usersitemid)) {	// calculate the user field forced value for item insert dialog
					$usersfield = $trklib->get_tracker_field($usersfieldid);
					$usersTrackerForced = [$usersfield['permName'] => $userinfo['login']];
					$smarty->assign('usersTrackerForced', $usersTrackerForced);
				}
			}
		}
	}

	if ($prefs['email_due'] > 0) {
		$userinfo['daysSinceEmailConfirm'] =  floor(($userlib->now - $userinfo['email_confirm']) / (60 * 60 * 24));
	}
} else {
	$userinfo['login'] = '';
	$userinfo['email'] = '';
	$userinfo['created'] = $tikilib->now;
	$userinfo['registrationDate'] = '';
	$userinfo['age'] = '';
	$userinfo['currentLogin'] = '';
	$userinfo['editable'] = true;

	$_REQUEST['user'] = 0;
}

if ($tiki_p_admin == 'y') {
	$all_groups = $userlib->list_all_groups();
} else {
	foreach ($userGroups as $g => $t) {
		$all_groups[] = $g;
	}
}

//add tablesorter sorting and filtering
$tsOn = Table_Check::isEnabled(true);

$smarty->assign('tsOn', $tsOn);
$tsAjax = Table_Check::isAjaxCall();
$smarty->assign('tsAjax', $tsAjax);
static $iid = 0;
++$iid;
$ts_tableid = 'adminusers' . $iid;
$smarty->assign('ts_tableid', $ts_tableid);

if (!$tsOn || ($tsOn && $tsAjax)) {
	$users = $userlib->get_users(
		$offset,
		$numrows,
		$sort_mode,
		$find,
		$initial,
		true,
		$filterGroup,
		$filterEmail,
		!empty($_REQUEST['filterEmailNotConfirmed']),
		!empty($_REQUEST['filterNotValidated']),
		!empty($_REQUEST['filterNeverLoggedIn'])
	);
}
if ($tsOn && !$tsAjax) {
	$users['cant'] = $userlib->count_users('');
	$users['data'] = $users['cant'] > 0 ? true : false;
	//delete anonymous out of group list used for dropdown
	$ts_groups = array_flip($all_groups);
	unset($ts_groups['Anonymous']);
	$ts_groups = array_flip($ts_groups);
	//set tablesorter code
	Table_Factory::build(
		'TikiAdminusers',
		array(
			'id' => $ts_tableid,
			'total' => $users['cant'],
			'columns' => array(
				 '#groups' => array(
					 'filter' => array(
						 'options' => $ts_groups
				 	)
				)
			 ),
		)
	);
}

$smarty->assign_by_ref('users', $users['data']);
$smarty->assign_by_ref('cant', $users['cant']);

if (isset($_REQUEST['add'])) {
	$cookietab = '2';
}

if (count($errors) > 0) {
	exit_with_error_messages($errors);
}

if (isset($_POST['ajaxtype'])) {
	$smarty->assign('ajaxfeedback', 'y');
	$ajaxpost = array_intersect_key($_POST, [
		'ajaxtype' => '',
		'ajaxheading' => '',
		'ajaxitems' => '',
		'ajaxmsg' => '',
		'ajaxtoMsg' => '',
		'ajaxtoList' => '',
	]);
	$smarty->assign($ajaxpost);
}
$smarty->assign_by_ref('all_groups', $all_groups);
$smarty->assign('userinfo', $userinfo);
$smarty->assign('userId', $_REQUEST['user']);
$smarty->assign('username', $username);
$smarty->assign('usermail', $usermail);
$smarty->assign_by_ref('tikifeedback', $tikifeedback);
$smarty->assign('cookietab', $cookietab);
$smarty->assign('uses_tabs', 'y');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-adminusers.tpl');
if ($tsAjax) {
	$smarty->display('tiki-adminusers.tpl');
} else {
	$smarty->display('tiki.tpl');
}

/**
 * @param $errors
 */
function exit_with_error_messages($errors)
{
	$access = TikiLib::lib('access');
	$message = '';

	foreach ($errors as $an_error) {
		$message .= $an_error['mes'] . ".<p>\n";
	}

	$message .= '<p>' . tra('Please go back and try again') . '.';
	$access->display_error(tra('Could not create user'), $message);
}
