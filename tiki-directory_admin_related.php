<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-directory_admin_related.php,v 1.17 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/directory/dirlib.php');

if ($prefs['feature_directory'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_directory");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_directory_cats != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

// If no parent category then the parent category is 0
if (!isset($_REQUEST["parent"]))
	$_REQUEST["parent"] = 0;

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
  $area = 'deldirrelated';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$dirlib->dir_remove_related($_REQUEST["parent"], $_REQUEST["categId"]);
	} else {
		key_get($area);
	}
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
// What are we paginating: items
$items = $dirlib->dir_list_related_categories($_REQUEST["parent"], $offset, $maxRecords, $sort_mode, $find);
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('items', $items["data"]);

$categs = $dirlib->dir_get_all_categories_np(0, -1, 'name_asc', $find, $_REQUEST["parent"]);
$smarty->assign('categs', $categs);
$all_categs = $dirlib->dir_get_all_categories(0, -1, 'name_asc', $find);
$smarty->assign('all_categs', $all_categs);

// This page should be displayed with Directory section options
$section='directory';
include_once('tiki-section_options.php');

ask_ticket('dir-admin-related');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-directory_admin_related.tpl');
$smarty->display("tiki.tpl");

?>
