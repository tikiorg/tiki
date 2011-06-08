<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script is used to assign groups to a particular user
// ASSIGN USER TO GROUPS
require_once ('tiki-setup.php');

$auto_query_args = array('sort_mode', 'offset', 'find', 'assign_user', 'group', 'maxRecords');

$access->check_permission_either(array('tiki_p_admin_users', 'tiki_p_subscribe_groups'));

if (!isset($_REQUEST["assign_user"]) || ($tiki_p_admin != 'y' && $tiki_p_admin_users != 'y')) {
	$_REQUEST['assign_user'] = $user;
	$userChoice = 'y';
	$smarty->assign_by_ref('userChoice', $userChoice);
} else {
	if (!$userlib->user_exists($_REQUEST['assign_user'])) {
		$smarty->assign('msg', tra("User doesn't exist"));
		$smarty->display("error.tpl");
		die;
	}
	$userChoice = '';
	$smarty->assign_by_ref('assign_user', $_REQUEST['assign_user']);
}

$assign_user = $_REQUEST["assign_user"];

if (isset($_REQUEST["action"])) {
	check_ticket('admin-assign-user');
	
	if (!isset($_REQUEST["group"])) {
		$smarty->assign('msg', tra("You have to indicate a group"));
		$smarty->display("error.tpl");
		die;
	}
	if ($userChoice == 'y') {
		$gps = $userlib->get_groups(0, -1, 'groupName_asc', '', '', '', '', $userChoice);
		$groups = array();
		foreach($gps['data'] as $g) {
			$groups[$g['groupName']] = $g;
		}
	} elseif ($tiki_p_admin != 'y') {
		$groups = $userlib->get_user_groups_inclusion($user);
	}
	if ($_REQUEST["action"] == 'assign') {
		if (!$userlib->group_exists($_REQUEST["group"])) {
			$smarty->assign('msg', tra("This group is invalid"));
			$smarty->display("error.tpl");
			die;
		}
		if ($tiki_p_admin_users == 'y' ||($tiki_p_admin_users == 'y' && array_key_exists($_REQUEST["group"], $groups))) {
			$userlib->assign_user_to_group($_REQUEST["assign_user"], $_REQUEST["group"]);
			$logslib->add_log('perms',sprintf("Assigned %s in group %s",$_REQUEST["assign_user"], $_REQUEST["group"]));
		}			
	} elseif ($_REQUEST["action"] == 'removegroup' && ($tiki_p_admin == 'y' || ($tiki_p_admin_users == 'y' && array_key_exists($_REQUEST["group"], $groups)))) {
		$access->check_authenticity();
		$userlib->remove_user_from_group($_REQUEST["assign_user"], $_REQUEST["group"]);
		$logslib->add_log('perms',sprintf("Removed %s from group %s",$_REQUEST["assign_user"], $_REQUEST["group"]));
	}
}

if(isset($_REQUEST['set_default'])) {
	$userlib->set_default_group($_REQUEST['login'],$_REQUEST['defaultgroup']);
}

$user_info = $userlib->get_user_info($assign_user,true);
$smarty->assign_by_ref('user_info', $user_info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'groupName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if (isset($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
}

if ($tiki_p_admin != 'y' && $userChoice != 'y') {
	$ingroups = $userlib->get_user_groups_inclusion($user);
	foreach ($user_info['groups'] as $grp=>$i) {
		if (!isset($ingroups[$grp])) {
			unset($user_info['groups'][$grp]);
		}
	}
} else
	$ingroups = '';
$users = $userlib->get_groups($offset, $maxRecords, $sort_mode, $find,'','y', $ingroups, $userChoice);

foreach ($users['data'] as $key=>$gr) {
	if (isset($user_info['groups'][$gr['groupName']])) {
		$users['data'][$key]['what'] = $user_info['groups'][$gr['groupName']];
	}
}
			
$smarty->assign_by_ref('cant_pages', $users["cant"]);

// Get users (list of users)
$smarty->assign_by_ref('users', $users["data"]);

ask_ticket('admin-assign-user');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-assignuser.tpl');
$smarty->display("tiki.tpl");
