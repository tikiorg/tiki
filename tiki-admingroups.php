<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = [
	[
		'staticKeyFilters' => [
			'groupstracker'             => 'int',
			'groupfield'                => 'int',
			'userstracker'              => 'int',
			'usersfield'                => 'int',
			'registrationUsersFieldIds' => 'digitscolons',
			'watch'                     => 'striptags',
			'unwatch'                   => 'striptags',
			'home'                      => 'pagename',
			'defcat'                    => 'int',
			'theme'                     => 'themename',
			'color'                     => 'striptags',
			'maxRecords'                => 'int',
			'membersMax'                => 'int',
			'bannedMax'                 => 'int',
			'sort_mode'                 => 'alnumdash',
			'sort_mode_member'          => 'alnumdash',
			'bannedSort'                => 'alnumdash',
			'offset'                    => 'int',
			'membersOffset'             => 'int',
			'bannedOffset'              => 'int',
			'initial'                   => 'alpha',
			'find'                      => 'groupname',
			'group'                     => 'groupname',
		]
	]
];

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
	if (!empty($user) && $access->checkOrigin()) {
		$tikilib = TikiLib::lib('tiki');
		if ( isset($_REQUEST['watch'])) {
			$tikilib->add_user_watch($user, 'user_joins_group', $_REQUEST['watch'], 'group');
		} else if ( isset($_REQUEST['unwatch'])) {
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

if (isset($_REQUEST['clean']) && $access->checkOrigin()) {
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

//add tablesorter sorting and filtering for main group list
$ts = Table_Check::setVars('admingroups', true);
if ($ts['enabled'] && !$ts['ajax']) {
	//set tablesorter code
	Table_Factory::build('TikiAdminGroups', ['id' => $ts['tableid'], 'total' => $users['cant']]);
}

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
	if (isset($re["groupColor"])) $groupcolor = $re["groupColor"];
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
		!empty($re['registrationUsersFieldIds'])
			?  $smarty->assign('registrationUsersFieldIds', $re['registrationUsersFieldIds'])
			: $smarty->assign('registrationUsersFieldIds', '');
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

	//group members
	if (!isset($_REQUEST['membersOffset'])) $_REQUEST['membersOffset'] = 0;
	if (empty($_REQUEST['sort_mode_member'])) $_REQUEST['sort_mode_member'] = 'login_asc';
	$membersMax = isset($_REQUEST['membersMax']) && is_numeric($_REQUEST['membersMax'])
		? $_REQUEST['membersMax'] : $prefs['maxRecords'];
	$memberslist = $userlib->get_group_users($_REQUEST['group'], $_REQUEST['membersOffset'], $membersMax, '*',
		$_REQUEST['sort_mode_member']);
	if ($re['expireAfter'] > 0) {
		foreach ($memberslist as $i=>$member) {
			if (empty($member['expire'])) {
				$memberslist[$i]['expire'] = $member['created'] + ($re['expireAfter'] * 24*60*60);
			}
		}
	}
	$membersCount = $userlib->count_users($_REQUEST['group']);
	$smarty->assign('membersCount', $membersCount);
	$smarty->assign('membersOffset', $_REQUEST['membersOffset']);
	$smarty->assign('memberslist', $memberslist);

	//banned members of a group
	$bannedOffset = isset($_REQUEST['bannedOffset']) ? $_REQUEST['bannedOffset'] : 0;
	$bannedMax = isset($_REQUEST['bannedMax']) ? $_REQUEST['bannedMax'] : $prefs['maxRecords'];
	if (empty($_REQUEST['bannedSort'])) {
		$bannedSort = ['source_itemId' => 'asc'];
	} elseif (!empty($_REQUEST['bannedSort']) && substr($_REQUEST['bannedSort'], -4) === 'desc') {
		$bannedSort = ['source_itemId' => 'desc'];
	} else {
		$bannedSort = ['source_itemId' => 'asc'];
	}
	$bannedlist = $userlib->get_group_banned_users($_REQUEST['group'], $bannedOffset, $bannedMax, null, $bannedSort);
	$smarty->assign('bannedlist', $bannedlist['data']);
	$smarty->assign('bannedCount', $bannedlist['cant']);

	$userslist=$userlib->list_all_users();
	if (!empty($memberslist)) {
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

	if ($ts['enabled'] && !$ts['ajax']) {
		Table_Factory::build(
			'TikiAdminGroupsMembers',
			[
				'id' => 'groupsMembers',
				'total' => $membersCount,
				'ajax' => [
					'requiredparams' => [
						'group' => $_REQUEST['group']
					]
				]
			]
		);
		Table_Factory::build(
			'TikiAdminGroupsBanned',
			[
				'id' => 'bannedMembers',
				'total' => $bannedlist['cant'],
				'ajax' => [
					'requiredparams' => [
						'group' => $_REQUEST['group']
					]
				]
			]
		);
	}

	if (!empty($user)) {
		 $re['isWatching'] = TikiLib::lib('tiki')->user_watches($user, 'user_joins_group', $groupname, 'group') > 0;
	} else {
		 $re['isWatching'] = false;
	}
	$cookietab = "2";
} else {
	$allgroups = $userlib->list_all_groups();
	foreach ($allgroups as $rr) {
		$inc["$rr"] = "n";
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
if (!empty($_REQUEST['group']) && isset($_REQUEST['import']) && $access->checkOrigin()) {
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
		Feedback::error($errors);
	}
	$cookietab = 3; // members list tab
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

$smarty->assign('inc', $inc);
$smarty->assign('group', $_REQUEST["group"]);
$smarty->assign('groupname', $groupname);
$smarty->assign('groupdesc', $groupdesc);
$smarty->assign('grouphome', $grouphome);
$smarty->assign('groupdefcat', $groupdefcat);
$smarty->assign('grouptheme', $grouptheme);
$smarty->assign('groupcolor', $groupcolor);
$smarty->assign('groupperms', $groupperms);
$smarty->assign_by_ref('userChoice', $userChoice);
$smarty->assign_by_ref('cant_pages', $users["cant"]);
$smarty->assign('group_info', $re);

ask_ticket('admin-groups');

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
