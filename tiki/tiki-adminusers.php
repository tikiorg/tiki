<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-adminusers.php,v 1.76.2.6 2008-03-13 16:54:36 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$tikifeedback = array();
require_once ('tiki-setup.php');

if (!($user == 'admin' || $tiki_p_admin == 'y' || $tiki_p_admin_users == 'y')) { // temporary patch: tiki_p_admin includes tiki_p_admin_users but if you don't clean the temp/cache each time you sqlupgrade the perms setting is not synchornous with the cache
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

function discardUser($u, $reason) {
	$u['reason'] = $reason;
	return $u;
}

function batchImportUsers() {
	$patterns['login'] = "/^[-_a-zA-Z0-9@\.]*$/";
	global $userlib, $smarty, $logslib, $tiki_p_admin, $user, $prefs;

	$fname = $_FILES['csvlist']['tmp_name'];
	$fhandle = fopen($fname, "r");
	$fields = fgetcsv($fhandle, 1000);
	if (!$fields[0]) {
		$smarty->assign('msg', tra("The file is not a CSV file or has not a correct syntax"));
		$smarty->display("error.tpl");
		die;
	}
	if ($fields[0]!="login" && $fields[0]!="password" && $fields[0]!="email" && $fields[0]!="groups") {
		$smarty->assign('msg', tra("The file does not have the required header:")." login, email, password, groups");
		$smarty->display("error.tpl");
		die;	
	}	
	while (!feof($fhandle)) {
		$data = fgetcsv($fhandle, 1000);
		if (empty($data))
			continue;
		$temp_max = count($fields);
		for ($i = 0; $i < $temp_max; $i++) {
			if ($fields[$i] == "login" && function_exists("mb_detect_encoding") && mb_detect_encoding($data[$i], "ASCII, UTF-8, ISO-8859-1") ==  "ISO-8859-1") {
				$data[$i] = utf8_encode($data[$i]);
			}
			@$ar[$fields[$i]] = $data[$i];
		}
		$userrecs[] = $ar;
	}
	fclose ($fhandle);
	if (empty($userrecs) or !is_array($userrecs)) {
		$smarty->assign('msg', tra("No records were found. Check the file please!"));
		$smarty->display("error.tpl");
		die;
	}
	$added = 0;
	$errors = array();
	$discarded = array();
	if ($tiki_p_admin != 'y')
		$userGroups = $userlib->get_user_groups_inclusion($user);
	foreach ($userrecs as $u) {
		$local = array();
		$exist = false;
		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			if (empty($u['login']) && empty($u['email'])) {
				$local[] = discardUser($u, tra("User login or email is required"));
			} else { // pick up the info on the master
				$info = $userlib->interGetUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], empty($u['login'])?'':$u['login'], empty($u['email'])?'':$u['email']);
				if (empty($info)) {
					$local[] = discardUser($u, tra("User does not exist on master"));
				} else {
					$u['login'] = $info['login'];
					$u['email'] = $info['email'];
				}
			}
		} else {
			if (empty($u['login'])) {
				$local[] = discardUser($u, tra("User login is required"));
			}
			if (empty($u['password'])) {
				$local[] = discardUser($u, tra("Password is required"));
			}
			if (empty($u['email'])) {
				$local[] = discardUser($u, tra("Email is required"));
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
			$discarded[] = discardUser($u, tra("User is duplicated"));
			continue;
		}
		if (!$exist) {
			$pass_first_login = (isset($_REQUEST['pass_first_login']) && $_REQUEST['pass_first_login'] == 'on') ? true: false;
			$userlib->add_user($u['login'], $u['password'], $u['email'], '', $pass_first_login);
			$logslib->add_log('users',sprintf(tra("Created account %s <%s>"),$u['login'], $u['email']));
		}
		$userlib->set_user_fields($u);

		if ($exist && isset($_REQUEST['overwriteGroup'])) {
			$userlib->remove_user_from_all_groups($u['login']);
		}

			if (@$u['groups']) {
				$grps = explode(",", $u['groups']);

				foreach ($grps as $grp) {
					$grp = preg_replace("/^ *(.*) *$/u", "$1", $grp);
					if (!$userlib->group_exists($grp)) {
						$err = tra("Unknown").": $grp";
						if (!in_array($err, $errors))
								$errors[] = $err;
					} elseif ($tiki_p_admin != 'y' &&  !array_key_exists($grp, $userGroups)) {
						$err = tra("Permission denied").": $grp";
						if (!in_array($err, $errors))
								$errors[] = $err;
					} else {
						$userlib->assign_user_to_group($u['login'], $grp);
						$logslib->add_log('perms',sprintf(tra("Assigned %s in group %s"),$u["login"], $grp));
					}
				}
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
		$smarty->assign_by_ref('errors', $errors);
	}
}
$cookietab = "1";

// Process the form to add a user here
if (isset($_REQUEST["newuser"])) {
	check_ticket('admin-users');
	// if no user data entered, check if it's a batch upload  
	if ((!$_REQUEST["name"]) and (is_uploaded_file($_FILES['csvlist']['tmp_name']))) {
		batchImportUsers();
	} else {
		// Check if the user already exists
		if ($_REQUEST["pass"] != $_REQUEST["pass2"]) {
			$tikifeedback[] = array('num'=>1,'mes'=>tra("The passwords do not match"));
		} else {
			if ($userlib->user_exists($_REQUEST["name"])) {
				$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("User %s already exists"),$_REQUEST["name"]));
			} elseif (!preg_match($patterns['login'],$_REQUEST['name'])) {
				$tikifeedback[] = array('num'=>1,'mes'=>tra("User login contains invalid characters"));
			} else {
				$pass_first_login = (isset($_REQUEST['pass_first_login']) && $_REQUEST['pass_first_login'] == 'on') ? true: false;

				$polerr = $userlib->check_password_policy($_POST["pass"]);
				if ( strlen($polerr)>0 ) {
					$smarty->assign('msg',$polerr);
				    $smarty->display("error.tpl");
				    die;
				}
				if ($userlib->add_user($_REQUEST["name"], $_REQUEST["pass"], $_REQUEST["email"], '', $pass_first_login)) {
					$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("New %s created with %s %s."),tra("user"),tra("username"),$_REQUEST["name"]));
					$cookietab = '1';
					$_REQUEST['find'] = $_REQUEST["name"];
				} else {
					$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Impossible to create new %s with %s %s."),tra("user"),tra("username"),$_REQUEST["name"]));
				}
			}
		}
		if (isset($tikifeedback[0]['msg'])) {
			$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
		}
	}
} elseif (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == 'delete' && $_REQUEST["user"] != 'admin') {
		$area = 'deluser';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$userlib->remove_user($_REQUEST["user"]);
			$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s %s successfully deleted."),tra("user"),$_REQUEST["user"]));
		} else {
			key_get($area);
		}
	}
	if ($_REQUEST["action"] == 'removegroup') {
		$area = 'deluserfromgroup';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$userlib->remove_user_from_group($_REQUEST["user"], $_REQUEST["group"]);
			$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s %s removed from %s %s."),tra("user"),$_REQUEST["user"],tra("group"),$_REQUEST["group"]));
		} else {
			key_get($area);
		}
	}
	$_REQUEST["user"] = '';
	if (isset($tikifeedback[0]['msg'])) {
		$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
	}					
} elseif (!empty($_REQUEST["submit_mult"]) && !empty($_REQUEST["checked"])) {
	if ($_REQUEST['submit_mult'] == 'remove_users' || $_REQUEST['submit_mult'] == 'remove_users_with_page') {
		$area = 'batchdeluser';
		if ($prefs['feature_ticketlib2'] == 'n' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			foreach ($_REQUEST["checked"] as $deleteuser) if ( $deleteuser != 'admin' ) {
				$userlib->remove_user($deleteuser);
				if ($_REQUEST['submit_mult'] == 'remove_users_with_page')
					$tikilib->remove_all_versions($prefs['feature_wiki_userpage_prefix'].$deleteuser);
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s <b>%s</b> successfully deleted."),tra("user"),$deleteuser));
			}
		} elseif ( $prefs['feature_ticketlib2'] == 'y') {
			$ch = "";
			foreach ($_REQUEST['checked'] as $c) {
				$ch .= "&amp;checked[]=".urlencode($c);
			}
			key_get($area, "", "tiki-adminusers.php?submit_mult=".$_REQUEST['submit_mult'].$ch);
		} else {
			key_get($area);
		}
	} elseif ($_REQUEST['submit_mult'] == 'assign_groups') {
		$group_management_mode = TRUE;
		$smarty->assign('group_management_mode', 'y');
		$sort_mode = 'groupName_asc';
		$initial = '';
		$find = '';
		if ($tiki_p_admin != 'y')
			$userGroups = $userlib->get_user_groups_inclusion($user);
		else
			$userGroups = '';
		$groups = $userlib->get_groups(0, -1, $sort_mode, $find, $initial, 'n', $userGroups);
		$smarty->assign('groups', $groups['data']);
	} elseif ($_REQUEST['submit_mult'] == 'set_default_groups') {
		$set_default_groups_mode = TRUE;
		$smarty->assign('set_default_groups_mode', 'y');
		$sort_mode = 'groupName_asc';
		$initial = '';
		$find = '';
		if ($tiki_p_admin != 'y')
			$userGroups = $userlib->get_user_groups_inclusion($user);
		else
			$userGroups = '';
		$groups = $userlib->get_groups(0, -1, $sort_mode, $find, $initial, 'n', $userGroups);
		$smarty->assign('groups', $groups['data']);
	}
	if (isset($tikifeedback[0]['msg'])) {
		$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
	}					
} elseif (!empty($_REQUEST['group_management']) && $_REQUEST['group_management'] == 'add') {
	if (!empty($_REQUEST["checked_groups"]) && !empty($_REQUEST["checked"])) {
		if ($tiki_p_admin != 'y')
			$userGroups = $userlib->get_user_groups_inclusion($user);
		foreach ($_REQUEST['checked'] as $assign_user) {
			foreach ($_REQUEST["checked_groups"] as $group) {
				if ($tiki_p_admin == 'y' || array_key_exists($group, $userGroups)) {					
					$userlib->assign_user_to_group($assign_user, $group);
					$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s <b>%s</b> assigned to %s <b>%s</b>."),tra("user"),$assign_user,tra("group"),$group));
				}
			}
		}
	}
	if (isset($tikifeedback[0]['msg'])) {
		$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
	}					
} elseif (!empty($_REQUEST['group_management']) && $_REQUEST['group_management'] == 'remove') {
	if (!empty($_REQUEST["checked_groups"]) && !empty($_REQUEST["checked"])) {
		foreach ($_REQUEST['checked'] as $assign_user) {
			foreach ($_REQUEST["checked_groups"] as $group) {
				$userlib->remove_user_from_group($assign_user, $group);
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s <b>%s</b> removed from %s <b>%s</b>."),tra("user"),$assign_user,tra("group"),$group));
			}
		}
	}
	if (isset($tikifeedback[0]['msg'])) {
		$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
	}					
} elseif (!empty($_REQUEST['set_default_groups']) && $_REQUEST['set_default_groups'] == 'y') {
	if (!empty($_REQUEST["checked_group"]) && !empty($_REQUEST["checked"])) {
		foreach ($_REQUEST['checked'] as $assign_user) {
			$group = $_REQUEST["checked_group"];
			$userlib->set_default_group($assign_user, $group);
			$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("group <b>%s</b> set as the default group of user <b>%s</b>."),$group,$assign_user));
		}
	}
	if (isset($tikifeedback[0]['msg'])) {
		$logslib->add_log('adminusers','',$tikifeedback[0]['msg']);
	}					
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'login_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}
$smarty->assign_by_ref('numrows', $numrows);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);


if (isset($_REQUEST["initial"])) {
	$initial = $_REQUEST["initial"];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);
$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if (isset($_REQUEST["filterGroup"])) {
	$filterGroup = $_REQUEST["filterGroup"];
} else {
	$filterGroup = '';
}
$smarty->assign('filterGroup', $filterGroup);

if (isset($_REQUEST["filterEmail"])) {
	$filterEmail = $_REQUEST["filterEmail"];
} else {
	$filterEmail = '';
}
$smarty->assign('filterEmail', $filterEmail);


//$users = $userlib->get_users($offset, $numrows, $sort_mode, $find, $initial, true);
$users = $userlib->get_users($offset, $numrows, $sort_mode, $find, $initial, true, $filterGroup, $filterEmail);

if (!empty($group_management_mode) || !empty($set_default_groups_mode)) {
	$arraylen = count($users['data']);
	for ($i=0; $i<$arraylen; $i++) {
		if (in_array($users['data'][$i]['user'], $_REQUEST["checked"])) {
			$users['data'][$i]['checked'] = 'y';
		}
	}
}

$smarty->assign_by_ref('users', $users["data"]);
$cant_pages = ceil($users["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($users["cant"] > ($offset + $numrows)) {
	$smarty->assign('next_offset', $offset + $numrows);
} else {
	$smarty->assign('next_offset', -1);
}
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $numrows);
} else {
	$smarty->assign('prev_offset', -1);
}

list($username,$usermail,$usersTrackerId,$chlogin) = array('','','',false);
if (isset($_REQUEST["user"]) and $_REQUEST["user"]) {
	if (!is_numeric($_REQUEST["user"])) {
		$_REQUEST["user"] = $userlib->get_user_id($_REQUEST["user"]);
	}
	$userinfo = $userlib->get_userid_info($_REQUEST["user"]);

	// If login is e-mail, email field needs to be the same as name (and is generally not send)
	if ( $prefs['login_is_email'] == 'y' && isset($_POST['name']) ) $_POST['email'] = $_POST['name'];

	if (isset($_POST["edituser"]) and isset($_POST['name']) and isset($_POST['email'])) {
		//var_dump($_POST);die;
		if ($_POST['name']) {
			if ( $userinfo['login'] != $_POST['name'] && $userinfo['login'] != 'admin' ) {
				if ($userlib->user_exists($_POST['name'])) {
					$smarty->assign('msg', tra("User already exists"));
			  	$smarty->display("error.tpl");
					die;
				}
				$chlogin = true;
			}
		}
		if (isset($_POST['pass']) &&  $_POST["pass"]) {
			if ($_POST["pass"] != $_POST["pass2"]) {
				$smarty->assign('msg', tra("The passwords do not match"));
				$smarty->display("error.tpl");
				die;
			} 
			$polerr = $userlib->check_password_policy($_POST["pass"]);
			if ( strlen($polerr)>0 ) {
				$smarty->assign('msg',$polerr);
			    $smarty->display("error.tpl");
			    die;
			}
			if ($userlib->change_user_password($_POST['name'],$_POST["pass"])) {
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s modified successfully."),tra("password")));
				$logslib->add_log('adminusers','changed password for '.$_POST['name']);
			} else {
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s modification failed."),tra("password")));
			}
		}
		if ($userinfo['email'] != $_POST['email']) {
			if ($userlib->change_user_email($_POST['name'],$_POST['email'],'')) {
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s changed from %s to %s"),tra("email"),$userinfo['email'],$_POST["email"]));
				$logslib->add_log('adminusers','changed email for '.$_POST['name'].' from '.$userinfo['email'].' to '.$_POST["email"]);
				$userinfo['email'] = $_POST['email'];
			} else {
				$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Impossible to change %s from %s to %s"),tra("email"),$userinfo['email'],$_POST["email"]));
			}
		}
		if ($chlogin) {
			if (!preg_match($patterns['login'],$_POST['name'])) {
				$tikifeedback[] = array('num'=>1,'mes'=>tra("Login contains invalid characters"));
			} elseif ($userlib->change_login($userinfo['login'],$_POST['name'])) {
				$tikifeedback[] = array('num'=>0,'mes'=>sprintf(tra("%s changed from %s to %s"),tra("login"),$userinfo['login'],$_POST["name"]));
				$logslib->add_log('adminusers','changed login for '.$_POST['name'].' from '.$userinfo['login'].' to '.$_POST["name"]);
				$userinfo['login'] = $_POST['name'];
			} else {
				$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Impossible to change %s from %s to %s"),tra("login"),$userinfo['email'],$_POST["email"]));
			}
		}
		setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab1");
	}
	if ($prefs['userTracker'] == 'y') {
		$re = $userlib->get_usertracker($_REQUEST["user"]);
		if ($re['usersTrackerId']) {
			include_once('lib/trackers/trackerlib.php');
			$userstrackerid = $re["usersTrackerId"];
			$smarty->assign('userstrackerid',$userstrackerid);
			$usersFields = $trklib->list_tracker_fields($usersTrackerId, 0, -1, 'position_asc', '');
			$smarty->assign_by_ref('usersFields', $usersFields['data']);
			if (isset($re["usersFieldId"]) and $re["usersFieldId"]) {
				$usersfieldid = $re["usersFieldId"];
				$smarty->assign('usersfieldid',$usersfieldid);
				$usersitemid = $trklib->get_item_id($userstrackerid,$usersfieldid,$re["user"]);
				$smarty->assign('usersitemid',$usersitemid);
			}
		}
	}
	$cookietab = "2";
} else {
	$userinfo['login'] = '';
	$userinfo['email'] = '';
	$userinfo['created'] = $tikilib->now;
	$userinfo['registrationDate'] = '';
	$userinfo['age'] = '';
	$userinfo['currentLogin'] = '';
	$cookietab = "1";
	$_REQUEST["user"] = 0;
}
if (isset($_REQUEST['add'])) {
	$cookietab = "2";
}
$all_groups = $userlib->get_groups();
$smarty->assign_by_ref('all_groups', $all_groups['data']);

$smarty->assign('userinfo', $userinfo);
$smarty->assign('userId', $_REQUEST["user"]);
$smarty->assign('username', $username);
$smarty->assign('usermail', $usermail);

$smarty->assign_by_ref('tikifeedback', $tikifeedback);

setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);

ask_ticket('admin-users');

$smarty->assign('uses_tabs', 'y');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-adminusers.tpl');
$smarty->display("tiki.tpl");
?>
