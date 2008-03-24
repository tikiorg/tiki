<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-categpermissions.php,v 1.17.2.1 2007-12-07 05:56:38 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

if ($prefs['feature_categories'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_categories");
	$smarty->display("error.tpl");
	die;
}

global $categlib;
if (!is_object($categlib)) {
	include_once('lib/categories/categlib.php');
}
global $cachelib;
global $userlib;

// Get the category from the request var
if (!isset($_REQUEST['categId'])) {
	$smarty->assign('msg', tra('No category indicated'));

	$smarty->display("error.tpl");
	die;
} else {
	$categId = $_REQUEST['categId'];

	$smarty->assign_by_ref('catId', $_REQUEST['categId']);
}

// Now check permissions to access this page
if ($tiki_p_admin_categories != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra('Permission denied; you cannot assign permissions for this category'));

	$smarty->display("error.tpl");
	die;
}

// Process the form to assign a new permission to this category
if (isset($_REQUEST['assign'])) {
	$userlib->assign_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
}

// Process the form to assign a new permission to this category and all children
if (isset($_REQUEST['assign_all'])) {
	$userlib->assign_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
	$children = $categlib->get_child_categories($categId);
	foreach ($children as $child) {
		$userlib->assign_object_permission($_REQUEST['group'], $child['categId'], 'category', $_REQUEST['perm']);
	}
}

// Process the form to remove a permission from the category
if (isset($_REQUEST['action'])) {
	$area = 'removecategperm';
	if ($_REQUEST['action'] == 'remove') {
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$userlib->remove_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
		} else {
			key_get($area);
		}
	} elseif ($_REQUEST['action'] == 'remove_all') {
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$userlib->remove_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
		$children = $categlib->get_child_categories($categId);
		foreach ($children as $child) {
			$userlib->remove_object_permission($_REQUEST['group'], $child['categId'], 'category', $_REQUEST['perm']);
		}
		} else {
			key_get($area);
		}
	}
}

// Now we have to get the individual page permissions if any
$category_perms = $userlib->get_object_permissions($categId, 'category');
$smarty->assign_by_ref('category_perms', $category_perms);

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'groupName_desc');
$smarty->assign_by_ref('groups', $groups['data']);

// Get a list of permissions
if (!$cachelib->isCached("categories_permission_names")) {
	$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'category');
	$cachelib->cacheItem("categories_permission_names",serialize($perms));
} else {
	$perms = unserialize($cachelib->getCached("categories_permission_names"));
}
$smarty->assign_by_ref('perms', $perms['data']);

// Get the category path
$path = $categlib->get_category_path($categId);
$smarty->assign_by_ref('path', $path);

$smarty->assign('mid', 'tiki-categpermissions.tpl');
$smarty->display("tiki.tpl");

?>
