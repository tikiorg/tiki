<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-g-admin_roles.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/ProcessManager.php');

// The galaxia roles manager PHP script.
if ($feature_workflow != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST['pid'])) {
	$smarty->assign('msg', tra("No process indicated"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$smarty->assign('pid', $_REQUEST['pid']);

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph'] = "lib/Galaxia/Processes/" . $proc_info['normalized_name'] . "/graph/" . $proc_info['normalized_name'] . ".png";

// Retrieve activity info if we are editing, assign to 
// default values when creating a new activity
if (!isset($_REQUEST['roleId']))
	$_REQUEST['roleId'] = 0;

if ($_REQUEST["roleId"]) {
	$info = $roleManager->get_role($_REQUEST['pid'], $_REQUEST["roleId"]);
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'roleId' => 0
	);
}

$smarty->assign('roleId', $_REQUEST['roleId']);
$smarty->assign('info', $info);

// Delete roles
if (isset($_REQUEST["delete"])) {
	foreach (array_keys($_REQUEST["role"])as $item) {
		$roleManager->remove_role($_REQUEST['pid'], $item);
	}
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save'])) {
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
	);

	$roleManager->replace_role($_REQUEST['pid'], $_REQUEST['roleId'], $vars);

	$info = array(
		'name' => '',
		'description' => '',
		'roleId' => 0
	);

	$smarty->assign('info', $info);
}

// MAPIING
if (!isset($_REQUEST['find_users']))
	$_REQUEST['find_users'] = '';

$smarty->assign('find_users', $_REQUEST['find_users']);
$users = $userlib->get_users(0, -1, 'login_asc', $_REQUEST['find_users']);
$smarty->assign_by_ref('users', $users['data']);

$groups = $userlib->get_groups(0, -1, 'groupName_asc', '');
$smarty->assign_by_ref('groups', $groups['data']);

$roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, 'name_asc', '');
$smarty->assign_by_ref('roles', $roles['data']);

if (isset($_REQUEST["delete_map"])) {
	foreach (array_keys($_REQUEST["map"])as $item) {
		$parts = explode(':::', $item);

		$roleManager->remove_mapping($parts[0], $parts[1]);
	}
}

if (isset($_REQUEST['mapg'])) {
	if ($_REQUEST['op'] == 'add') {
		$users = $userlib->get_group_users($_REQUEST['group']);

		foreach ($users as $a_user) {
			$roleManager->map_user_to_role($_REQUEST['pid'], $a_user, $_REQUEST['role']);
		}
	} else {
		$users = $userlib->get_group_users($_REQUEST['group']);

		foreach ($users as $a_user) {
			$roleManager->remove_mapping($a_user, $_REQUEST['role']);
		}
	}
}

if (isset($_REQUEST['save_map'])) {
	if (isset($_REQUEST['user']) && isset($_REQUEST['role'])) {
		foreach ($_REQUEST['user'] as $a_user) {
			foreach ($_REQUEST['role'] as $role) {
				$roleManager->map_user_to_role($_REQUEST['pid'], $a_user, $role);
			}
		}
	}
}

// list mappings
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

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
$smarty->assign_by_ref('sort_mode', $sort_mode);
$mapitems = $roleManager->list_mappings($_REQUEST['pid'], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign('cant', $mapitems['cant']);
$cant_pages = ceil($mapitems["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($mapitems["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('mapitems', $mapitems["data"]);

//MAPPING
if (!isset($_REQUEST['sort_mode2']))
	$_REQUEST['sort_mode2'] = 'name_asc';

$smarty->assign('sort_mode2', $_REQUEST['sort_mode2']);
// Get all the process roles
$all_roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, $_REQUEST['sort_mode2'], '');
$smarty->assign_by_ref('items', $all_roles['data']);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$proc_info['isValid'] = $valid ? 'y' : 'n';
$errors = array();

if (!$valid) {
	$errors = $activityManager->get_error();
}

$smarty->assign('errors', $errors);
$smarty->assign('proc_info', $proc_info);
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'offset2',
	'find2',
	'sort_mode2',
	'where2',
	'processId'
);

$smarty->assign('mid', 'tiki-g-admin_roles.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>