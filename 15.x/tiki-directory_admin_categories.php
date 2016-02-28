<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/directory/dirlib.php');
$access->check_feature('feature_directory');
$access->check_permission('tiki_p_admin_directory_cats');

// If no parent category then the parent category is 0
if (!isset($_REQUEST["parent"])) $_REQUEST["parent"] = 0;
$smarty->assign('parent', $_REQUEST["parent"]);
if ($_REQUEST["parent"] == 0) {
	$parent_name = 'Top';
} else {
	$parent_info = $dirlib->dir_get_category($_REQUEST['parent']);
	$parent_name = $parent_info['name'];
}
$smarty->assign('parent_name', $parent_name);
// Now get the path to the parent category
$path = $dirlib->dir_get_category_path_admin($_REQUEST["parent"]);
$smarty->assign_by_ref('path', $path);
// If no category is being edited set it to zero
if (!isset($_REQUEST["categId"])) $_REQUEST["categId"] = 0;
$smarty->assign('categId', $_REQUEST["categId"]);
// If we are editing an existing category then get the category information
// If not initialize the information to zero
if ($_REQUEST["categId"]) {
	$info = $dirlib->dir_get_category($_REQUEST["categId"]);
} else {
	$info = array();
	$info["name"] = '';
	$info["childrenType"] = 'c';
	$info["viewableChildren"] = 3;
	$info["allowSites"] = 'y';
	$info["showCount"] = 'y';
	$info["editorGroup"] = 'admin';
}
$smarty->assign_by_ref('info', $info);
// Remove a category
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$dirlib->dir_remove_category($_REQUEST["remove"]);
}
// Replace (add or edit) a category
if (isset($_REQUEST["save"])) {
	check_ticket('dir-add-categ');
	if (isset($_REQUEST["allowSites"]) && $_REQUEST["allowSites"] == 'on') $_REQUEST["allowSites"] = 'y';
	else $_REQUEST["allowSites"] = 'n';
	if (isset($_REQUEST["showCount"]) && $_REQUEST["showCount"] == 'on') $_REQUEST["showCount"] = 'y';
	else $_REQUEST["showCount"] = 'n';
	$categId = $dirlib->dir_replace_category($_REQUEST["parent"], $_REQUEST["categId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["childrenType"], $_REQUEST["viewableChildren"], $_REQUEST["allowSites"], $_REQUEST["showCount"], $_REQUEST["editorGroup"]);
	$cat_type = 'directory';
	$cat_objid = $categId;
	$cat_desc = substr($_REQUEST['description'], 0, 200);
	$cat_name = $_REQUEST['name'];
	$cat_href = 'tiki-directory_browse.php?parent=' . $cat_objid;
	include_once ("categorize.php");
	if ($_REQUEST["categId"]) {
		$info = $dirlib->dir_get_category($_REQUEST["categId"]);
	}
}
// Listing: categories in the parent category
// Pagination resolution
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
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign('find', $find);
$items = $dirlib->dir_list_categories($_REQUEST["parent"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);
$categs = $dirlib->dir_get_all_categories(0, -1, 'name_asc', $find);
$smarty->assign('categs', $categs);
$cat_type = 'directory';
$cat_objid = $_REQUEST['categId'];
include_once ("categorize_list.php");
// This page should be displayed with Directory section options
$section = 'directory';
include_once ('tiki-section_options.php');
ask_ticket('dir-add-categ');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-directory_admin_categories.tpl');
$smarty->display("tiki.tpl");
