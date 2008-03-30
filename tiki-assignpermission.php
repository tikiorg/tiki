<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-assignpermission.php,v 1.35.2.1 2007-12-29 16:30:00 jyhem Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script is used to assign permissions to a particular group
// ASSIGN PERMISSIONS TO GROUPS
// Initialization
require_once ('tiki-setup.php');

if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("You do not have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}
}

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
		$area = 'delpermassign';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$userlib->remove_permission_from_group($_REQUEST["permission"], $group);
			$logslib->add_log('perms',"unassigned perm ".$_REQUEST['permission']." from group $group");
		} else {
			key_get($area, sprintf(tra('Unassign perm %s from group %s'), $_REQUEST['permission'], $group));
		}
	}
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
		$userlib->change_permission_level($per, $_REQUEST['level'][$per]);

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

$perms = $userlib->get_permissions(0, -1, $sort_mode, $find, $_REQUEST["type"], $group);

foreach ($perms['data'] as $perm) {
 	if ($perm['admin'] == 'y' && $perm['hasPerm'] == 'y') {
		foreach ($perms['data'] as $key=>$p) {
			if ($p['type'] == $perm['type'] && $perm['permName'] != $p['permName']) {
				$perms['data'][$key]['from_admin'] = 'y';
				$perms['data'][$key]['hasPerm'] = 'y';
			}
		}
	}
}

if ($group != 'Anonymous') {
	// Get the list of permissions for anony
	$ifa = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],'Anonymous');
	$smarty->assign_by_ref('inherited_from_anon', $ifa['data']);
	if ($group != 'Registered') {
		$ifr = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],'Registered');
		$smarty->assign_by_ref('inherited_from_reg', $ifr['data']);
		$incgroups = $userlib->get_included_groups($group);
		foreach($incgroups as $ig) {
			$ixr = $userlib->get_permissions(0, -1, $sort_mode, $find,$_REQUEST["type"],$ig);
			$back[$ig] = $ixr['data'];
		}
		$smarty->assign_by_ref('inherited_groups_perms',$back);
	}
}

// Get the list of permissions
$group_info = $userlib->get_group_info($group);
$smarty->assign_by_ref('group_info', $group_info);

// Get users (list of users)
$smarty->assign_by_ref('perms', $perms["data"]);

ask_ticket('admin-perms');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-assignpermission.tpl');
$smarty->display("tiki.tpl");

?>
