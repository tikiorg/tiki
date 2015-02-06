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
// Remove a relationship
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$dirlib->dir_remove_related($_REQUEST["parent"], $_REQUEST["categId"]);
}
// Update a relationship
if (isset($_REQUEST["update"])) {
	check_ticket('dir-admin-related');
	$dirlib->dir_remove_related($_REQUEST["parent"], $_REQUEST["oldcategId"]);
	$dirlib->dir_add_categ_rel($_REQUEST["parent"], $_REQUEST["categId"]);
}
// Add a relationship
if (isset($_REQUEST["add"])) {
	check_ticket('dir-admin-related');
	$dirlib->dir_add_categ_rel($_REQUEST["parent"], $_REQUEST["categId"]);
	if (isset($_REQUEST["mutual"]) && $_REQUEST["mutual"] == 'on') {
		$dirlib->dir_add_categ_rel($_REQUEST["categId"], $_REQUEST["parent"]);
	}
}
// Listing: categories in the parent category
// Pagination resolution
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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
$items = $dirlib->dir_list_related_categories($_REQUEST["parent"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
$categs = $dirlib->dir_get_all_categories_np(0, -1, 'name_asc', $find, $_REQUEST["parent"]);
$smarty->assign('categs', $categs);
$all_categs = $dirlib->dir_get_all_categories(0, -1, 'name_asc', $find);
$smarty->assign('all_categs', $all_categs);
// This page should be displayed with Directory section options
$section = 'directory';
include_once ('tiki-section_options.php');
ask_ticket('dir-admin-related');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-directory_admin_related.tpl');
$smarty->display("tiki.tpl");
