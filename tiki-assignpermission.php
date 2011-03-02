<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script is used to assign permissions to a particular group
// ASSIGN PERMISSIONS TO GROUPS
require_once ('tiki-setup.php');

$access->check_permission('tiki_p_admin');

if (!isset($_REQUEST["group"])) {
	$smarty->assign('msg', tra("Unknown group"));

	$smarty->display("error.tpl");
	die;
}

$group = $_REQUEST["group"];

if (!$userlib->group_exists($group)) {
	$smarty->assign('msg', tra("Group doesn't exist"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign_by_ref('group', $group);

if (isset($_REQUEST['allper'])) {
	check_ticket('admin-perms');	
	foreach ($_REQUEST['level'] as $t_level) { 
		if ($_REQUEST['oper'] == 'assign') {
			$userlib->assign_level_permissions($group, $t_level);
			$logslib->add_log('perms',"assigned all perms level ".$t_level." to group $group");
		} else {
			$userlib->remove_level_permissions($group, $t_level);
			$logslib->add_log('perms',"unassigned all perms level ".$t_level." from group $group");
		}
	}
}

if (isset($_REQUEST["action"])) {
	check_ticket('admin-perms');
	if ($_REQUEST["action"] == 'assign') {
		$userlib->assign_permission_to_group($_REQUEST["perm"], $group);
		$logslib->add_log('perms',"assigned perm ".$_REQUEST['perm']." to group $group");
	}

	if ($_REQUEST["action"] == 'remove') {
		$access->check_authenticity(sprintf(tra('Unassign perm %s from group %s'), $_REQUEST['permission'], $group));
		$userlib->remove_permission_from_group($_REQUEST["permission"], $group);
		$logslib->add_log('perms',"unassigned perm ".$_REQUEST['permission']." from group $group");
	}
}
if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'temp_cache') {
	$cachelib->erase_dir_content("temp/cache/$tikidomain");
	$logslib->add_log('system','erased temp/cache content');
}

$types = $userlib->get_permissions_types();
$smarty->assign('types', $types);

$groups = $userlib->get_groups();
$smarty->assign('groups', $groups['data']);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'type_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (!isset($_REQUEST["type"])) {
	$_REQUEST["type"] = '';
}

$smarty->assign('type', $_REQUEST["type"]);

if (isset($_REQUEST["advanced_features"])) {
    $smarty->assign('advanced_features', $_REQUEST["advanced_features"]);
}

if (isset($_REQUEST["createlevel"])) {
	check_ticket('admin-perms');
	$userlib->create_dummy_level($_REQUEST['level']);
	$logslib->add_log('perms',"created level ".$_REQUEST['level']);
}

if (isset($_REQUEST['update'])) {
	check_ticket('admin-perms');
	foreach (array_keys($_REQUEST['permName'])as $per) {
		if (isset( $_REQUEST['level'][$per])) {
			$userlib->change_permission_level($per, $_REQUEST['level'][$per]);
		}

		if (isset($_REQUEST['perm'][$per])) {
			$userlib->assign_permission_to_group($per, $group);
		} else {
			$userlib->remove_permission_from_group($per, $group);
		}
		$logslib->add_log('perms',"changed perms for group $group");
	}
}

$levels = $userlib->get_permission_levels();
sort($levels);
$smarty->assign('levels', $levels);

// Get the list of permissions
$group_info = $userlib->get_group_info($group);
$smarty->assign_by_ref('group_info', $group_info);

$perms = $userlib->get_permissions(0, -1, $sort_mode, $find, $_REQUEST["type"], $group);

foreach ($perms['data'] as $perm) {
 	if ($perm['admin'] == 'y' && $perm['hasPerm'] == 'y') {
		foreach ($perms['data'] as $key=>$p) {
			if ($p['type'] == $perm['type'] && $perm['permName'] != $p['permName'] && !in_array($p['permName'], $group_info['perms'])) {
				$perms['data'][$key]['from_admin'] = 'y';
				$perms['data'][$key]['hasPerm'] = 'y';
			}
		}
	}
}

// If Anonymous is not always included in other groups unless explicitly specified as in 4.0, then the following should not execute, but commented remain here for reference as per other comment by jonnyb in tikilib.php get_user_groups()
//if ($group != 'Anonymous') {
	// Get the list of permissions for anony
	//$ifa = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],'Anonymous');
	//$smarty->assign_by_ref('inherited_from_anon', $ifa['data']);
//}

if ($group != 'Registered' && $group != 'Anonymous') {
	$ifr = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],'Registered');
	$smarty->assign_by_ref('inherited_from_reg', $ifr['data']);
}

$incgroups = $userlib->get_included_groups($group);
foreach($incgroups as $ig) {
	$ixr = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],$ig);
	$back[$ig] = $ixr['data'];
}
$smarty->assign_by_ref('inherited_groups_perms',$back);

// Get users (list of users)
$smarty->assign_by_ref('perms', $perms["data"]);

ask_ticket('admin-perms');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-assignpermission.tpl');
$smarty->display("tiki.tpl");
