<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-objectpermissions.php,v 1.25.2.2 2008-03-11 15:17:54 nyloth Exp $
include_once ("tiki-setup.php");
if (!isset($_REQUEST['objectName']) || empty($_REQUEST['objectType']) || empty($_REQUEST['objectId']) || empty($_REQUEST['permType'])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));
	$smarty->display("error.tpl");
	die;
}
$auto_query_args = array(
	'referer',
	'reloff',
	'objectName',
	'objectType',
	'permType',
	'objectId',
);
$perm = 'tiki_p_assign_perm_' . str_replace(' ', '_', $_REQUEST['objectType']);
if ($_REQUEST['objectType'] == 'wiki page') {
	if ($tiki_p_admin_wiki == 'y') {
		$special_perm = 'y';
	} else {
		$info = $tikilib->get_page_info($_REQUEST['objectName']);
		$tikilib->get_perm_object($_REQUEST['objectId'], $_REQUEST['objectType'], $info);
	}
} else {
	$tikilib->get_perm_object($_REQUEST['objectId'], $_REQUEST['objectType']);
	if ($_REQUEST['objectType'] == 'tracker') {
		global $trklib;
		include ('lib/trackers/trackerlib.php');
		if ($groupCreatorFieldId = $trklib->get_field_id_from_type($_REQUEST['objectId'], 'g', '1%')) {
			$smarty->assign('group_tracker', 'y');
		}
	}
}
if (!($tiki_p_admin_objects == 'y' || (isset($$perm) && $$perm == 'y') || (isset($special_perm) && $special_perm == 'y'))) {
	$smarty->assign('errortype', 401);
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
	global $structlib;
	include_once ('lib/structures/structlib.php');
	$pageInfoTree = $structlib->s_get_structure_pages($structlib->get_struct_ref_id($_REQUEST['objectId']));
	if (count($pageInfoTree) > 1) {
		$smarty->assign('inStructure', 'y');
	}
}

//Quickperms
$databaseperms = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"], '', true);
foreach($databaseperms['data'] as $perm) {
	if ($perm['level']=='basic')
		$quickperms_['basic'][$perm['permName']] = $perm['permName'];
	elseif ($perm['level']=='registered')
		$quickperms_['registered'][$perm['permName']] = $perm['permName'];
	elseif ($perm['level']=='editors')
		$quickperms_['editors'][$perm['permName']] = $perm['permName'];
	elseif ($perm['level']=='admin')
		$quickperms_['admin'][$perm['permName']] = $perm['permName'];
}

if(!isset($quickperms_['basic']))
	$quickperms_['basic'] = array();
if(!isset($quickperms_['registered']))
	$quickperms_['registered'] = array();
if(!isset($quickperms_['editors']))
	$quickperms_['editors'] = array();
if(!isset($quickperms_['admin']))
$quickperms_['admin'] = array();

$perms = array();
$perms['basic']['name'] = "basic";
$perms['basic']['data'] = array_merge($quickperms_['basic']);
$perms['registered']['name'] = "registered";
$perms['registered']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered']);
$perms['editors']['name'] = "editors";
$perms['editors']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered'], $quickperms_['editors']);
$perms['admin']['name'] = "admin";
$perms['admin']['data'] = array_merge($quickperms_['basic'], $quickperms_['registered'], $quickperms_['editors'], $quickperms_['admin']);
$perms['none']['name'] = "none";
$perms['none']['data'] = array();

//Test to map permissions of ile galleries into read write admin admin levels.
if($_REQUEST["permType"]=="file galleries") {
	unset($perms);
	$quickperms_temp['tiki_p_admin_file_galleries'] = 'tiki_p_admin_file_galleries';
	$quickperms_temp['tiki_p_assign_perm_file_gallery'] = 'tiki_p_assign_perm_file_gallery';
	$quickperms_temp['tiki_p_batch_upload_files'] = 'tiki_p_batch_upload_files';
	$quickperms_temp['tiki_p_batch_upload_file_dir'] = 'tiki_p_batch_upload_file_dir';
	$quickperms_temp['tiki_p_create_file_galleries'] = 'tiki_p_create_file_galleries';
	$quickperms_temp['tiki_p_download_files'] = 'tiki_p_download_files';
	$quickperms_temp['tiki_p_edit_gallery_file'] = 'tiki_p_edit_gallery_file';
	$quickperms_temp['tiki_p_list_file_galleries'] = 'tiki_p_list_file_galleries';
	$quickperms_temp['tiki_p_upload_files'] = 'tiki_p_upload_files';
	$quickperms_temp['tiki_p_view_fgal_explorer'] = 'tiki_p_view_fgal_explorer';
	$quickperms_temp['tiki_p_view_fgal_path'] = 'tiki_p_view_fgal_path';
	$quickperms_temp['tiki_p_view_file_gallery'] = 'tiki_p_view_file_gallery';
	$perms['admin']['name'] = "admin";
	$perms['admin']['data'] = $quickperms_temp;
	
	unset($quickperms_temp['tiki_p_admin_file_galleries']);
	unset($quickperms_temp['tiki_p_assign_perm_file_gallery']);
	$perms['write']['name'] = "write";
	$perms['write']['data'] = $quickperms_temp;
	
	unset($quickperms_temp['tiki_p_batch_upload_files']);
	unset($quickperms_temp['tiki_p_batch_upload_file_dir']);
	unset($quickperms_temp['tiki_p_create_file_galleries']);
	unset($quickperms_temp['tiki_p_edit_gallery_file']);
	unset($quickperms_temp['tiki_p_upload_files']);
	$perms['read']['name'] = "read";
	$perms['read']['data'] = $quickperms_temp;
	
	$perms['none']['name'] = "none";
	$perms['none']['data'] = array();
}

$smarty->assign('quickperms', $perms);

if (isset($_REQUEST['assign']) && isset($_REQUEST['quick_perms'])) {
	check_ticket('object-perms');
	
	$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	
	foreach($groups['data'] as $group) {
		if(isset($_REQUEST["perm_".$group['groupName']])) {
			$group = $group['groupName'];
			$permission = $_REQUEST["perm_".$group];
			
			if ($permission != "userdefined") {
				//Remove all permissions of a group
				
				foreach($perms['admin']['data'] as $perm) {
					$userlib->remove_object_permission($group, $_REQUEST["objectId"], $_REQUEST["objectType"], $perm);
				}
				
				//Add chosen quickperm bundle to the objcet/group
				foreach($perms["$permission"]['data'] as $perm) {
					$userlib->assign_object_permission($group, $_REQUEST["objectId"], $_REQUEST["objectType"], $perm);				
				}
			}
		}
	}
}
//Quickperm END

// Process the form to assign a new permission to this page
elseif (isset($_REQUEST['assign']) && isset($_REQUEST['group']) && isset($_REQUEST['perm'])) {
	check_ticket('object-perms');
	foreach($_REQUEST['perm'] as $perm) {
		if ($tiki_p_admin_objects != 'y' && !$userlib->user_has_permission($user, $perm)) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display('error.tpl');
			die;
		}
	}
	if (!empty($_REQUEST['assignstructure']) && $_REQUEST['assignstructure'] == 'on' && !empty($pageInfoTree)) {
		foreach($pageInfoTree as $subPage) {
			foreach($_REQUEST['perm'] as $perm) {
				foreach($_REQUEST['group'] as $group) {
					$userlib->assign_object_permission($group, $subPage["pageName"], 'wiki page', $perm);
				}
			}
		}
	} else {
		foreach($_REQUEST['perm'] as $perm) {
			foreach($_REQUEST['group'] as $group) {
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
	foreach($_REQUEST['checked'] as $perm) {
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
//Quickperm
foreach($page_perms as $perm) {
	$current_permissions[$perm['groupName']][] = $perm['permName'];
}
//Quickperm END

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');

//Quickperm
foreach($groups['data'] as $key=>$group) {
	foreach($perms as $perm) {
		if (!empty($current_permissions[$group['groupName']]) && is_array($current_permissions[$group['groupName']])) {
			//Check if Group has admin perm.
			$diff1 = array_diff($current_permissions[$group['groupName']], $perms[$perm['name']]['data']);
			$diff2 = array_diff($perms[$perm['name']]['data'], $current_permissions[$group['groupName']]);
			if (empty($diff1) AND empty($diff2)) {
				$groups['data'][$key]['groupSumm'] = $perm['name'];
				break;
			}
		} else {
			$groups['data'][$key]['groupSumm'] = "none";
			break;
		}
	}
	//If Group has NO perm.
	if (empty($groups['data'][$key]['groupSumm']))
		$groups['data'][$key]['groupSumm'] = "userdefined";
}
//Quickperm END

$smarty->assign_by_ref('groups', $groups["data"]);
// Get a list of permissions
$perms = $userlib->get_permissions(0, -1, 'permName_asc', '', $_REQUEST["permType"], '', true);

if ($tiki_p_admin_objects != 'y') {
	$userPerms = array();
	foreach($perms['data'] as $perm) {
		if ($userlib->user_has_permission($user, $perm['permName'])) {
			$userPerms[] = $perm;
		}
	}
	$smarty->assign_by_ref('perms', $userPerms);
} else {
	$smarty->assign_by_ref('perms', $perms['data']);
}
foreach($page_perms as $i => $pp) {
	foreach($perms['data'] as $p) {
		if ($pp['permName'] == $p['permName']) {
			$page_perms[$i]['permDesc'] = $p['permDesc'];
			break;
		}
	}
}
$smarty->assign_by_ref('page_perms', $page_perms);
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	include_once ('lib/categories/categlib.php');
	// Get the permissions of the categories that this object belongs to,
	$categ_perms = array();
	$parents = $categlib->get_object_categories($_REQUEST['objectType'], $_REQUEST['objectId']);
	$perms_categ = $userlib->get_permissions(0, -1, 'permName_asc', '', 'category');
	foreach($parents as $categId) {
		if ($userlib->object_has_one_permission($categId, 'category')) {
			$categ_perm = $userlib->get_object_permissions($categId, 'category');
			$categ_perm[0]['catpath'] = $categlib->get_category_name($categId);
			$categ_perms[] = $categ_perm;
		} else {
			$categpath = $categlib->get_category_path($categId);
			$arraysize = count($categpath);
			$x = 0;
			for ($i = $arraysize - 2; $i >= 0; $i--) {
				if ($userlib->object_has_one_permission($categpath[$i]['categId'], 'category')) {
					$categ_perms[] = $userlib->get_object_permissions($categpath[$i]['categId'], 'category');
					$categ_perms[$x][0]['catpath'] = $categlib->get_category_name($categpath[$i]['categId']);
					$x++;
					break 1;
				}
			}
		}
	}
	foreach($categ_perms as $i => $p) {
		foreach($p as $j => $pp) {
			foreach($perms_categ['data'] as $ppp) {
				if ($ppp['permName'] == $pp['permName']) {
					$categ_perms[$i][$j]['permDesc'] = $ppp['permDesc'];
					break;
				}
			}
		}
	}
	$smarty->assign_by_ref('categ_perms', $categ_perms);
}
ask_ticket('object-perms');
// Display the template
$smarty->assign('mid', 'tiki-objectpermissions.tpl');
if (isset($_REQUEST['filegals_manager']) && $_REQUEST['filegals_manager'] != '') {
	$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
	$smarty->display("tiki-print.tpl");
} else {
	$smarty->display("tiki.tpl");
}
