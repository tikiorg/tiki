<?php

// $Header:

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

if ($feature_categories != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_categories");
	$smarty->display("error.tpl");
	die;
}

// Get the category from the request var
if (!isset($_REQUEST['categId'])) {
	$smarty->assign('msg', tra('No category indicated'));

	$smarty->display("error.tpl");
	die;
} else {
	$categId = $_REQUEST['categId'];

	$smarty->assign_by_ref('categId', $_REQUEST['categId']);
}

include_once ('tiki-categsetup.php');

// Now check permissions to access this page
if ($tiki_p_admin_categories != 'y') {
	$smarty->assign('msg', tra('Permission denied; you cannot assign permissions for this category'));

	$smarty->display("error.tpl");
	die;
}

/*
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error.tpl");
	die;
}
*/

// Process the form to assign a new permission to this page
if (isset($_REQUEST['assign'])) {
	$userlib->assign_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
}

// Process the form to remove a permission from the page
if (isset($_REQUEST['action'])) {
	if ($_REQUEST['action'] == 'remove') {
		$userlib->remove_object_permission($_REQUEST['group'], $categId, 'category', $_REQUEST['perm']);
	}
}

// Now we have to get the individual page permissions if any
$category_perms = $userlib->get_object_permissions($categId, 'category');
$smarty->assign_by_ref('category_perms', $category_perms);

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'groupName_desc');
$smarty->assign_by_ref('groups', $groups['data']);

// Get a list of permissions
$perms = $userlib->get_permissions(0, -1, 'permName_desc', 'categories');
$smarty->assign_by_ref('perms', $perms['data']);

// Get the category path
$path = $categlib->get_category_path($categId);
$smarty->assign_by_ref('path', $path);

ask_ticket('category-perms');

$smarty->assign('mid', 'tiki-categpermissions.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>