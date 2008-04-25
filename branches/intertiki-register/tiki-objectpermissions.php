<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-objectpermissions.php,v 1.25.2.2 2008-03-11 15:17:54 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

if (!isset(
	$_REQUEST['objectName']) || empty($_REQUEST['objectType']) || empty($_REQUEST['objectId']) || empty($_REQUEST['permType'])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$smarty->display("error.tpl");
	die;
}
$perm = 'tiki_p_assign_perm_'.str_replace(' ', '_', $_REQUEST['objectType']);

if ($_REQUEST['objectType'] == 'wiki page') {
	if ($tiki_p_admin_wiki == 'y') {
		$special_perm = 'y';
	} else if ($prefs['wiki_creator_admin'] == 'y') {
		include_once ('lib/wiki/wikilib.php');
		$creator = $wikilib->get_creator($_REQUEST['objectName']);
		if ($creator && $user && ($creator == $user)) {
			$special_perm = 'y';
		}
	}
}

if (!($tiki_p_admin_objects == 'y' || (isset($$perm) && $$perm == 'y') ||(isset($special_perm) && $special_perm == 'y'))) {
	$smarty->assign('msg', tra("Permission denied you cannot assign permissions for this object"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["referer"])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_REQUEST["referer"] = $_SERVER['HTTP_REFERER'];
	}
}

if (isset($_REQUEST["referer"])) {
	$smarty->assign('referer', $_REQUEST["referer"]);
}


$_REQUEST["objectId"] = urldecode($_REQUEST["objectId"]);
$_REQUEST["objectType"] = urldecode($_REQUEST["objectType"]);
$_REQUEST["permType"] = urldecode($_REQUEST["permType"]);

$smarty->assign('objectName', $_REQUEST["objectName"]);
$smarty->assign('objectId', $_REQUEST["objectId"]);
$smarty->assign('objectType', $_REQUEST["objectType"]);
$smarty->assign('permType', $_REQUEST["permType"]);

if ($_REQUEST['objectType'] == 'wiki' || $_REQUEST['objectType'] == 'wiki page') {
	global $structlib; include_once('lib/structures/structlib.php');
	$pageInfoTree = $structlib->s_get_structure_pages($structlib->get_struct_ref_id($_REQUEST['objectId']));
	if (count($pageInfoTree) > 1) {
		$smarty->assign('inStructure', 'y');
	}
}

// Process the form to assign a new permission to this page
if (isset($_REQUEST['assign']) && isset($_REQUEST['group']) && isset($_REQUEST['perm'])) {
	check_ticket('object-perms');
	foreach($_REQUEST['perm'] as $perm) {
		if ($tiki_p_admin_objects != 'y' && !$userlib->user_has_permission($user, $perm)) {
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display('error.tpl');
			die;
		}
	}
	if (!empty($_REQUEST['assignstructure']) && $_REQUEST['assignstructure'] == 'on' && !empty($pageInfoTree)) {
		foreach($pageInfoTree as $subPage) {
			foreach($_REQUEST['perm'] as $perm) {
				foreach ($_REQUEST['group'] as $group) {
					$userlib->assign_object_permission($group,$subPage["pageName"],'wiki page',$perm);
				}
			}
		}
	} else {
		foreach($_REQUEST['perm'] as $perm) {
			foreach ($_REQUEST['group'] as $group) {
				$userlib->assign_object_permission($group, $_REQUEST["objectId"], $_REQUEST["objectType"], $perm);
			}
		}
	}
	$smarty->assign('groupName', $_REQUEST["group"]);
}

// Process the form to remove a permission from the page
if (isset($_REQUEST["action"])) {
	check_ticket('object-perms');
	if ($_REQUEST["action"] == 'remove') {
		$userlib->remove_object_permission($_REQUEST["group"], $_REQUEST["objectId"], $_REQUEST["objectType"], $_REQUEST["perm"]);
	}
}
if (isset($_REQUEST['delsel_x']) && isset($_REQUEST['checked'])) {
	check_ticket('object-perms');
	foreach ($_REQUEST['checked'] as $perm) {
		if (preg_match('/([^ ]*) (.*)/', $perm, $matches)) {
			if (!empty($_REQUEST['removestructure']) && $_REQUEST['removestructure'] == 'on' && !empty($pageInfoTree)) {
				foreach($pageInfoTree as $subPage) {
					$userlib->remove_object_permission($matches[2], $subPage['pageName'], $_REQUEST['objectType'], $matches[1]);
				}
			} else {
				$userlib->remove_object_permission($matches[2], $_REQUEST['objectId'], $_REQUEST['objectType'], $matches[1]);
			}
		}
	}
}

// Now we have to get the individual page permissions if any
$page_perms = $userlib->get_object_permissions($_REQUEST["objectId"], $_REQUEST["objectType"]);
$smarty->assign_by_ref('page_perms', $page_perms);

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
$smarty->assign_by_ref('groups', $groups["data"]);

// Get a list of permissions
$perms = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"]);
if ($tiki_p_admin_objects != 'y') {
	$userPerms = array();
	foreach ($perms['data'] as $perm) {
		if ($userlib->user_has_permission($user, $perm['permName'])) {
			$userPerms[] = $perm;
		}	
	}
	$smarty->assign_by_ref('perms', $userPerms);
} else {
	$smarty->assign_by_ref('perms', $perms['data']);
}

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');
	// Get the permissions of the categories that this object belongs to,
	$categ_perms = array();
	$parents = $categlib->get_object_categories($_REQUEST['objectType'], $_REQUEST['objectId']);
	foreach ($parents as $categId) {
		if ($userlib->object_has_one_permission($categId, 'category')) {
			$categ_perm = $userlib->get_object_permissions($categId, 'category');
			$categ_perm[0]['catpath'] = $categlib->get_category_name($categId);
			$categ_perms[] = $categ_perm;
		} else {
			$categpath = $categlib->get_category_path($categId);
			$arraysize = count($categpath);
			$x = 0;
			for ($i=$arraysize-2; $i>=0; $i--) {
				if ($userlib->object_has_one_permission($categpath[$i]['categId'], 'category')) {
					$categ_perms[] = $userlib->get_object_permissions($categpath[$i]['categId'], 'category');
					$categ_perms[$x][0]['catpath'] = $categlib->get_category_name($categpath[$i]['categId']);
					$x++;
					break 1;
				}
			}
		}
	}
	$smarty->assign_by_ref('categ_perms', $categ_perms);
}

ask_ticket('object-perms');

// Display the template
$smarty->assign('mid','tiki-objectpermissions.tpl');
if ( isset($_REQUEST['filegals_manager']) && $_REQUEST['filegals_manager'] == 'y' ) {
	$smarty->assign('filegals_manager','y');
	$smarty->display("tiki-print.tpl");
}  else {
	$smarty->display("tiki.tpl");
}
?>
