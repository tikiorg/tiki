<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_permission('tiki_p_admin');

$auto_query_args = array('group');

if (!isset($cookietab)) {
	$cookietab = '1';
}
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

if ($prefs['feature_user_watches'] == 'y') {
	if (!empty($user)) {
		$tikilib = TikiLib::lib('tiki');
		if ( isset($_REQUEST['watch'] ) ) {
			$tikilib->add_user_watch($user, 'user_joins_group', $_REQUEST['watch'], 'group');
		} else if ( isset($_REQUEST['unwatch'] ) ) {
			$tikilib->remove_user_watch($user, 'user_joins_group', $_REQUEST['unwatch'], 'group');
		}
	}
}

$ag_home = '';
$ag_defcat = 0;
$ag_theme = '';
if (isset($_REQUEST["home"])) $ag_home = $_REQUEST["home"];
if (!empty($_REQUEST["defcat"])) $ag_defcat = $_REQUEST["defcat"];
if (isset($_REQUEST["theme"])) $ag_theme = $_REQUEST["theme"];
// Process the form to add a group
if (isset($_REQUEST["newgroup"])) {
	$access->check_authenticity(tra('Are you sure you want to create a new group?'));
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
		$userlib->add_group($_REQUEST['name'], $_REQUEST['desc'], $ag_home, $ag_utracker, $ag_gtracker, '', $_REQUEST['userChoice'], $ag_defcat, $ag_theme, 0, 0, 'n', $_REQUEST['expireAfter'], $_REQUEST['emailPattern'], $_REQUEST['anniversary'], $_REQUEST['prorateInterval']);
		if (isset($_REQUEST["include_groups"])) {
			foreach ($_REQUEST["include_groups"] as $include) {
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
	$access->check_authenticity(tra('Are you sure you want to add this user?'));
	$user = $_REQUEST['user'];
	$group = $_REQUEST['group'];
	if ($user && $group) {
		if ($userlib->assign_user_to_group($user, $group)) {
			$logslib->add_log('admingroups', "added $user to $group");
		}
	}
	$cookietab = "3";
}

// banning

if (isset($_REQUEST['banuser'])) {
	$auser = $_REQUEST['user'];
	$agroup = $_REQUEST['group'];
	$access->check_authenticity(tr('Are you sure you want to ban the user "%0" from the group "%1"?', $auser, $agroup));
	$userlib->ban_user_from_group($auser, $agroup);
	$logslib->add_log('admingroups', "banned $auser from $agroup");
	$cookietab = "3";
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'unbanuser') {
	$auser = $_REQUEST['user'];
	$agroup = $_REQUEST['group'];
	$access->check_authenticity(tr('Are you sure you want to unban the user "%0" from the group "%1"?', $auser, $agroup));
	$userlib->unban_user_from_group($auser, $agroup);
	$logslib->add_log('admingroups', "unbanned $auser from $agroup");
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
	$userlib->change_group($_REQUEST['olgroup'], $_REQUEST['name'], $_REQUEST['desc'], $ag_home, $ag_utracker, $ag_gtracker, $ag_ufield, $ag_gfield, $ag_rufields, $_REQUEST['userChoice'], $ag_defcat, $ag_theme, 'n', $_REQUEST['expireAfter'], $_REQUEST['emailPattern'], $_REQUEST['anniversary'], $_REQUEST['prorateInterval']);
	$userlib->remove_all_inclusions($_REQUEST["name"]);
	if (isset($_REQUEST["include_groups"]) and is_array($_REQUEST["include_groups"])) {
		foreach ($_REQUEST["include_groups"] as $include) {
			if ($include && $_REQUEST["name"] != $include) {
				$userlib->group_inclusion($_REQUEST["name"], $include);
			}
		}
	}
	$_REQUEST["group"] = $_REQUEST["name"];
	$logslib->add_log('admingroups', 'modified group ' . $_REQUEST["olgroup"] . ' to ' . $_REQUEST["group"]);
	$cookietab = 1;
}
// Process a form to remove a group
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == 'delete') {
		$access->check_authenticity(tra('Remove group: ') . $_REQUEST['group']);
		$userlib->remove_group($_REQUEST["group"]);
		$logslib->add_log('admingroups', 'removed group ' . $_REQUEST["group"]);
		unset($_REQUEST['group']);
	}
}
// Unassign a list of members
if (isset($_REQUEST['unassign_members']) && isset($_REQUEST['submit_mult_members']) && $_REQUEST['submit_mult_members'] == 'unassign' && isset($_REQUEST['group']) && !in_array($_REQUEST['group'], array('Registered', 'Anonymous'))) {
	$access->check_authenticity(tra('Are you sure you want to unassign these users?'));
	foreach ($_REQUEST['members'] as $m) {
		$userlib->remove_user_from_group($userlib->get_user_login($m), $_REQUEST['group']);
	}
}
if (!empty($_REQUEST['submit_mult']) && !empty($_REQUEST['checked'])) {
	$access->check_authenticity(tra('Are you sure you want to delete these groups?'));
	foreach ($_REQUEST['checked'] as $delete) {
		if ($delete != 'Admins' && $delete != 'Anonymous' && $delete != 'Registered') {
			$userlib->remove_group($delete);
			$logslib->add_log('admingroups', 'removed group ' . $delete);
		}
	}
}
if (isset($_REQUEST['clean'])) {
	$cachelib = TikiLib::lib('cache');
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
list(	$groupname, $groupdesc, $grouphome, $userstrackerid, $usersfieldid, $grouptrackerid,
		$groupfieldid, $defcatfieldid, $themefieldid, $groupperms, $trackerinfo, $memberslist,
		$userChoice, $groupdefcat, $grouptheme, $expireAfter, $emailPattern, $anniversary, $prorateInterval) =
		array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

if (!empty($_REQUEST["group"])) {
	$re = $userlib->get_group_info($_REQUEST["group"]);
	if (isset($re["groupName"])) $groupname = $re["groupName"];
	if (isset($re["groupDesc"])) $groupdesc = $re["groupDesc"];
	if (isset($re["groupHome"])) $grouphome = $re["groupHome"];
	if (isset($re["groupDefCat"])) $groupdefcat = $re["groupDefCat"];
	if (isset($re["groupTheme"])) $grouptheme = $re["groupTheme"];
	if (isset($re['userChoice'])) $userChoice = $re['userChoice'];
	if (isset($re['expireAfter'])) $expireAfter = $re['expireAfter'];
	if (isset($re['anniversary'])) $anniversary = $re['anniversary'];
	if (isset($re['prorateInterval'])) $prorateInterval = $re['prorateInterval'];
	if ($prefs['userTracker'] == 'y') {
		if (isset($re["usersTrackerId"]) and $re["usersTrackerId"]) {
			$trklib = TikiLib::lib('trk');
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
			$trklib = TikiLib::lib('trk');
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
	foreach ($allgroups as $rr) {
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
	if (!empty($user)) {
		 $re['isWatching'] = TikiLib::lib('tiki')->user_watches($user, 'user_joins_group', $groupname, 'group') > 0;
	} else {
		 $re['isWatching'] = false;
	}
	if ($cookietab == '1' && !isset($_REQUEST["save"])) $cookietab = "2";
} else {
	$allgroups = $userlib->list_all_groups();
	foreach ($allgroups as $rr) {
		$inc["$rr"] = "n";
	}
	if (!isset($cookietab)) {
		$cookietab = '1';
	}
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
		$smarty->assign('msg', tra('The file has incorrect syntax or is not a CSV file'));
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
	$categlib = TikiLib::lib('categ');
	$categories = $categlib->getCategories();
	$smarty->assign_by_ref('categories', $categories);
}

if (isset($_REQUEST['group'])) {
	$smarty->assign('indirectly_inherited_groups', indirectly_inherited_groups($inc));
}
//group theme - list themes
$themelib = TikiLib::lib('theme');
$group_themes = $themelib->list_themes_and_options();
$smarty->assign_by_ref('group_themes', $group_themes);

$smarty->assign('memberslist', $memberslist);

$bannedlist = $userlib->get_group_banned_users($_REQUEST['group']);
$smarty->assign('bannedlist', $bannedlist);

$userslist=$userlib->list_all_users();
if (!empty($memberslist)) {
	if ($cookietab == 1 && !isset($_REQUEST["save"])) $cookietab = 3;
	foreach ($memberslist as $key => $values) {
		if ( in_array($values["login"], $userslist) ) {
			unset($userslist[array_search($values["login"], $userslist, true)]);
		}
	}
	foreach ($bannedlist as $key => $value) {
		if ( in_array($value, $userslist) ) {
			unset($userslist[array_search($value, $userslist, true)]);
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

/**
 * @param $direct_groups
 * @return array
 */
function indirectly_inherited_groups($direct_groups)
{
	$userlib = TikiLib::lib('user');
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
