<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_permission('tiki_p_admin');

$auto_query_args = array('group');

if (!isset($cookietab)) { $cookietab = '1'; }
list($trackers, $ag_utracker, $ag_ufield, $ag_gtracker, $ag_gfield, $ag_rufields) = array(array() ,	0, 0, 0, 0, '');
if (isset($prefs['groupTracker']) and $prefs['groupTracker'] == 'y') {
	$trklib = TikiLib::lib('trk');
	$trackerlist = $trklib->list_trackers(0, -1, 'name_asc', '');
	$trackers = $trackerlist['list'];
	if (isset($_REQUEST["groupstracker"]) and isset($trackers[$_REQUEST["groupstracker"]])) {
		$ag_gtracker = $_REQUEST["groupstracker"];
		if (isset($_REQUEST["groupfield"]) and $_REQUEST["groupfield"]) {
			$ag_gfield = $_REQUEST["groupfield"];
		}
	}
}
if (isset($prefs['userTracker']) and $prefs['userTracker'] == 'y') {
	$trklib = TikiLib::lib('trk');
	if (!isset($trackerlist)) $trackerlist = $trklib->list_trackers(0, -1, 'name_asc', '');
	$trackers = $trackerlist['list'];
	if (isset($_REQUEST["userstracker"]) and isset($trackers[$_REQUEST["userstracker"]])) {
		$ag_utracker = $_REQUEST["userstracker"];
		if (isset($_REQUEST["usersfield"]) and $_REQUEST["usersfield"]) {
			$ag_ufield = $_REQUEST["usersfield"];
		}
		if (!empty($_REQUEST['registrationUsersFieldIds'])) {
			$ag_rufields = $_REQUEST['registrationUsersFieldIds'];
		}
	}
}
$smarty->assign('trackers', $trackers);
$ag_home = '';
$ag_defcat = 0;
$ag_theme = '';
if (isset($_REQUEST["home"])) $ag_home = $_REQUEST["home"];
if (!empty($_REQUEST["defcat"])) $ag_defcat = $_REQUEST["defcat"];
if (isset($_REQUEST["theme"])) $ag_theme = $_REQUEST["theme"];
// Process the form to add a group
if (isset($_REQUEST["newgroup"])) {
	check_ticket('admin-groups');
	if (!empty($_REQUEST['name'])) $_REQUEST['name'] = trim($_REQUEST['name']);
	if (empty($_REQUEST['name'])) {
		$smarty->assign('msg', tra("Group name can not be empty"));
		$smarty->display("error.tpl");
		die;
	}
	// Check if the user already exists
	if ($userlib->group_exists($_REQUEST["name"])) {
		$smarty->assign('msg', tra("Group already exists"));
		$smarty->display("error.tpl");
		die;
	} else {
		$_REQUEST['userChoice'] = (isset($_REQUEST['userChoice']) && $_REQUEST['userChoice'] == 'on') ? 'y' : '';
		if (empty($_REQUEST['expireAfter'])) $_REQUEST['expireAfter'] = 0;
		$userlib->add_group($_REQUEST['name'], $_REQUEST['desc'], $ag_home, $ag_utracker, $ag_gtracker, '', $_REQUEST['userChoice'], $ag_defcat, $ag_theme, 0, 0, 'n', $_REQUEST['expireAfter'], $_REQUEST['emailPattern']);
		if (isset($_REQUEST["include_groups"])) {
			foreach($_REQUEST["include_groups"] as $include) {
				if ($_REQUEST["name"] != $include) {
					$userlib->group_inclusion($_REQUEST["name"], $include);
				}
			}
		}
	}
	$_REQUEST["group"] = $_REQUEST["name"];
	$logslib->add_log('admingroups', 'created group ' . $_REQUEST["group"]);
}
if (isset($_REQUEST['adduser'])) {
	$user = $_REQUEST['user'];
	$group = $_REQUEST['group'];
	if ($user && $group) {
		if ($userlib->assign_user_to_group($user, $group)) {
			$logslib->add_log('admingroups', "added $user to $group");
		}
	}
	$cookietab = "3";
}
// modification
if (isset($_REQUEST["save"]) and isset($_REQUEST["olgroup"]) and !empty($_REQUEST["name"])) {
	check_ticket('admin-groups');
	if ($_REQUEST['olgroup'] != $_REQUEST['name'] && $userlib->group_exists($_REQUEST['name'])) {
		$smarty->assign('msg', tra('Group already exists'));
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST['userChoice']) && $_REQUEST['userChoice'] == 'on') {
		$_REQUEST['userChoice'] = 'y';
	} else {
		$_REQUEST['userChoice'] = '';
	}
	if (empty($_REQUEST['expireAfter'])) $_REQUEST['expireAfter'] = 0;
	$userlib->change_group($_REQUEST['olgroup'], $_REQUEST['name'], $_REQUEST['desc'], $ag_home, $ag_utracker, $ag_gtracker, $ag_ufield, $ag_gfield, $ag_rufields, $_REQUEST['userChoice'], $ag_defcat, $ag_theme, 'n', $_REQUEST['expireAfter'], $_REQUEST['emailPattern']);
	$userlib->remove_all_inclusions($_REQUEST["name"]);
	if (isset($_REQUEST["include_groups"]) and is_array($_REQUEST["include_groups"])) {
		foreach($_REQUEST["include_groups"] as $include) {
			if ($include && $_REQUEST["name"] != $include) {
				$userlib->group_inclusion($_REQUEST["name"], $include);
			}
		}
	}
	$_REQUEST["group"] = $_REQUEST["name"];
	$logslib->add_log('admingroups', 'modified group ' . $_REQUEST["olgroup"] . ' to ' . $_REQUEST["group"]);
}
// Process a form to remove a group
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == 'delete') {
		$access->check_authenticity(tra('Remove group: ') . htmlspecialchars($_REQUEST['group']));
		$userlib->remove_group($_REQUEST["group"]);
		$logslib->add_log('admingroups', 'removed group ' . $_REQUEST["group"]);
		unset($_REQUEST['group']);
	}
}
if (isset($_REQUEST['clean'])) {
	global $cachelib;
	require_once ("lib/cache/cachelib.php");
	check_ticket('admin-groups');
	$cachelib->invalidate('grouplist');
	$cachelib->invalidate('groupIdlist');
}
if (!isset($_REQUEST['maxRecords'])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST['maxRecords'];
}
$smarty->assign_by_ref('maxRecords', $numrows);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'groupName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
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
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$users = $userlib->get_groups($offset, $numrows, $sort_mode, $find, $initial);
$inc = array();
list($groupname, $groupdesc, $grouphome, $userstrackerid, $usersfieldid, $grouptrackerid, $groupfieldid, $defcatfieldid, $themefieldid, $groupperms, $trackerinfo, $memberslist, $userChoice, $groupdefcat, $grouptheme, $expireAfter, $emailPattern) = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
if (!empty($_REQUEST["group"])) {
	$re = $userlib->get_group_info($_REQUEST["group"]);
	if (isset($re["groupName"])) $groupname = $re["groupName"];
	if (isset($re["groupDesc"])) $groupdesc = $re["groupDesc"];
	if (isset($re["groupHome"])) $grouphome = $re["groupHome"];
	if (isset($re["groupDefCat"])) $groupdefcat = $re["groupDefCat"];
	if (isset($re["groupTheme"])) $grouptheme = $re["groupTheme"];
	if (isset($re['userChoice'])) $userChoice = $re['userChoice'];
	if (isset($re['expireAfter'])) $expireAfter = $re['expireAfter'];
	if ($prefs['userTracker'] == 'y') {
		if (isset($re["usersTrackerId"]) and $re["usersTrackerId"]) {
			include_once ('lib/trackers/trackerlib.php');
			$userstrackerid = $re["usersTrackerId"];
			$smarty->assign('userstrackerid', $userstrackerid);
			$usersFields = $trklib->list_tracker_fields($userstrackerid, 0, -1, 'position_asc', '');
			$smarty->assign_by_ref('usersFields', $usersFields['data']);
			if (isset($re["usersFieldId"]) and $re["usersFieldId"]) {
				$usersfieldid = $re["usersFieldId"];
				$smarty->assign('usersfieldid', $usersfieldid);
			}
		}
		if (isset($re['registrationUsersFieldIds'])) $smarty->assign('registrationUsersFieldIds', $re['registrationUsersFieldIds']);
	}
	if ($prefs['groupTracker'] == 'y') {
		$groupFields = array();
		if (isset($re["groupTrackerId"]) and $re["groupTrackerId"]) {
			include_once ('lib/trackers/trackerlib.php');
			$grouptrackerid = $re["groupTrackerId"];
			$smarty->assign('grouptrackerid', $grouptrackerid);
			$groupFields = $trklib->list_tracker_fields($grouptrackerid, 0, -1, 'position_asc', '');
			$smarty->assign_by_ref('groupFields', $groupFields['data']);
			if (isset($re["groupFieldId"]) and $re["groupFieldId"]) {
				$groupfieldid = $re["groupFieldId"];
				$smarty->assign('groupfieldid', $groupfieldid);
				$groupitemid = $trklib->get_item_id($grouptrackerid, $groupfieldid, $groupname);
				$smarty->assign('groupitemid', $groupitemid);
			}
		}
	}
	$groupperms = $re["perms"];
	//$allgroups = $userlib->list_all_groups();
	$allgroups = $userlib->list_can_include_groups($re["groupName"]);
	$rs = $userlib->get_included_groups($_REQUEST['group'], false);
	foreach($allgroups as $rr) {
		$inc["$rr"] = "n";
		if (in_array($rr, $rs)) {
			$inc["$rr"] = "y";
			$smarty->assign('hasOneIncludedGroup', "y");
		}
	}
	if (!isset($_REQUEST['membersOffset'])) $_REQUEST['membersOffset'] = 0;
	if (empty($_REQUEST['sort_mode_member'])) $_REQUEST['sort_mode_member'] = 'login_asc';
	$memberslist = $userlib->get_group_users($_REQUEST['group'], $_REQUEST['membersOffset'], $prefs['maxRecords'], '*', $_REQUEST['sort_mode_member']);
	if ($re['expireAfter'] > 0) {
		foreach ($memberslist as $i=>$member) {
			if (empty($member['expire'])) {
				$memberslist[$i]['expire'] = $member['created'] + ($re['expireAfter'] * 24*60*60);
			}
		}
	}
	$smarty->assign('membersCount', $userlib->count_users($_REQUEST['group']));
	$smarty->assign('membersOffset', $_REQUEST['membersOffset']);
	if ($cookietab == '1') $cookietab = "2";
} else {
	$allgroups = $userlib->list_all_groups();
	foreach($allgroups as $rr) {
		$inc["$rr"] = "n";
	}
	if (!isset($cookietab)) { $cookietab = '1'; }
	$_REQUEST["group"] = 0;
}
if (isset($_REQUEST['add'])) {
	$cookietab = "2";
}
if (!empty($_REQUEST['group']) && isset($_REQUEST['export'])) {
	$users = $userlib->get_users(0, -1, 'login_asc', '', '', false, $_REQUEST['group']);
	$smarty->assign_by_ref('users', $users['data']);
	$listfields = array();
	if (isset($_REQUEST['username'])) {
		$listfields[] = 'user';
	}
	if (isset($_REQUEST['email'])) {
		$listfields[] = 'email';
	}
	if (isset($_REQUEST['lastLogin'])) {
		$listfields[] = 'lastLogin';
	}
	$smarty->assign_by_ref('listfields', $listfields);
	$data = $smarty->fetch('tiki-export_users.tpl');
	if (!empty($_REQUEST['encoding']) && $_REQUEST['encoding'] == 'ISO-8859-1') {
		$data = utf8_decode($data);
	} else {
		$_REQUEST['encoding'] = "UTF-8";
	}
	header("Content-type: text/comma-separated-values; charset:" . $_REQUEST['encoding']);
	header("Content-Disposition: attachment; filename=" . tra('users') . "_" . $_REQUEST['group'] . ".csv");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
	echo $data;
	die;
}
if (!empty($_REQUEST['group']) && isset($_REQUEST['import'])) {
	$fname = $_FILES['csvlist']['tmp_name'];
	$fhandle = fopen($fname, 'r');
	$fields = fgetcsv($fhandle, 1000);
	if (!$fields[0]) {
		$smarty->assign('msg', tra('The file is not a CSV file or has not a correct syntax'));
		$smarty->display('error.tpl');
		die;
	}
	if ($fields[0] != 'user') {
		$smarty->assign('msg', tra('The file does not have the required header:') . ' user');
		$smarty->display('error.tpl');
		die;
	}
	$data = @fgetcsv($fhandle, 1000);
	while (!feof($fhandle)) {
		if (function_exists("mb_detect_encoding") && mb_detect_encoding($data[0], "ASCII, UTF-8, ISO-8859-1") == "ISO-8859-1") {
			$data[0] = utf8_encode($data[0]);
		}
		$data[0] = trim($data[0]);
		if (!$userlib->user_exists($data[0])) {
			$errors[] = tra("User doesn't exist") . ': ' . $data[0];
		} else {
			$userlib->assign_user_to_group($data[0], $_REQUEST['group']);
		}
		$data = fgetcsv($fhandle, 1000);
	}
	if (!empty($errors)) {
		$smarty->assign_by_ref('errors', $errors);
	}
	$cookietab = 4;
}
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	include_once ('lib/categories/categlib.php');
	$categories = $categlib->get_all_categories_respect_perms($user, 'view_category');
	$smarty->assign_by_ref('categories', $categories);
}

if (isset($_REQUEST['group'])) {
	$smarty->assign('indirectly_inherited_groups', indirectly_inherited_groups($inc));
}
$av_themes = $tikilib->list_styles();
$smarty->assign_by_ref('av_themes', $av_themes);
$smarty->assign('memberslist', $memberslist);
$userslist=$userlib->list_all_users();
if (!empty($memberslist)) {
	foreach($memberslist as $key => $values){
		if ( in_array($values["login"],$userslist ) ) {
			unset($userslist[array_search($values["login"],$userslist,true)]);
		}
	}
}
$smarty->assign('userslist', $userslist);
$smarty->assign('inc', $inc);
$smarty->assign('group', $_REQUEST["group"]);
$smarty->assign('groupname', $groupname);
$smarty->assign('groupdesc', $groupdesc);
$smarty->assign('grouphome', $grouphome);
$smarty->assign('groupdefcat', $groupdefcat);
$smarty->assign('grouptheme', $grouptheme);
$smarty->assign('groupperms', $groupperms);
$smarty->assign_by_ref('userChoice', $userChoice);
$smarty->assign_by_ref('cant_pages', $users["cant"]);
$smarty->assign('group_info', $re);
setcookie('tab', $cookietab);
$smarty->assign('cookietab', $cookietab);
ask_ticket('admin-groups');
$smarty->assign('uses_tabs', 'y');
// Assign the list of groups
$smarty->assign_by_ref('users', $users["data"]);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template for group administration
$smarty->assign('mid', 'tiki-admingroups.tpl');
$smarty->display("tiki.tpl");

function indirectly_inherited_groups($direct_groups) {
	global $userlib;
	$indirect_groups = array();
	foreach ($direct_groups as $a_direct_group => $does_inherit) {
		if ($does_inherit === 'y') {
 			$some_indirect_groups = $userlib->get_included_groups($a_direct_group);
 			foreach ($some_indirect_groups as $an_indirect_group) {
 				$indirect_groups[] = $an_indirect_group;
 			}
 		}
 	}
 	return $indirect_groups;
}
