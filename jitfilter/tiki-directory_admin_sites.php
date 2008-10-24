<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-directory_admin_sites.php,v 1.22 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/directory/dirlib.php');

if ($prefs['feature_directory'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_directory");

	$smarty->display("error.tpl");
	die;
}

// If no parent category then the parent category is 0
if (!isset($_REQUEST["parent"]))
	$_REQUEST["parent"] = 0;

$smarty->assign('parent', $_REQUEST["parent"]);
$all = 0;

if ($_REQUEST["parent"] == 0) {
	$parent_name = 'Top';

	$all = 1;
} else {
	$parent_info = $dirlib->dir_get_category($_REQUEST['parent']);

	$parent_name = $parent_info['name'];
}

$smarty->assign('parent_name', $parent_name);

if (isset($parent_info) && $user) {
	if (in_array($parent_info['editorGroup'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_directory_sites = 'y';

		$smarty->assign('tiki_p_admin_directory_sites', 'y');
	}
}

if ($tiki_p_admin_directory_sites != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

// Now get the path to the parent category
$path = $dirlib->dir_get_category_path_admin($_REQUEST["parent"]);
$smarty->assign_by_ref('path', $path);

// If no site is being edited set it to zero
if (!isset($_REQUEST["siteId"]))
	$_REQUEST["siteId"] = 0;

$smarty->assign('siteId', $_REQUEST["siteId"]);

// If we are editing an existing category then get the category information
// If not initialize the information to zero
if ($_REQUEST["siteId"]) {
	$info = $dirlib->dir_get_site($_REQUEST["siteId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["url"] = '';
	$info["country"] = 'None';
	$info["isValid"] = 'y';
}

$smarty->assign_by_ref('info', $info);

// Remove a category
if (isset($_REQUEST["remove"])) {
	if (is_array($_REQUEST["remove"])) {
		check_ticket('dir-admin-sites');
		foreach ($_REQUEST["remove"] as $remid) {
			$dirlib->dir_remove_site($remid);
		}
	} else {
		$area = 'deldirsite';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$dirlib->dir_remove_site($_REQUEST["remove"]);
		} else {
			key_get($area);
		}
	}
}

// Replace (add or edit) a category
if (isset($_REQUEST["save"])) {
	check_ticket('dir-admin-sites');
	if (empty($_REQUEST["name"])) {
		$smarty->assign('msg', tra("Must enter a name to add a site"));

		$smarty->display("error.tpl");
		die;
	}

	if (empty($_REQUEST["url"])) {
		$smarty->assign('msg', tra("Must enter a url to add a site"));

		$smarty->display("error.tpl");
		die;
	}

	if ((substr($_REQUEST["url"], 0, 7) <> 'http://') &&
	    (substr($_REQUEST["url"], 0, 8) <> 'https://') &&
	    (substr($_REQUEST["url"], 0, 6) <> 'ftp://'))
	{
		$_REQUEST["url"] = 'http://' . $_REQUEST["url"];
	}

	if (!isset($_REQUEST["siteCats"]) || count($_REQUEST["siteCats"]) == 0) {
		$smarty->assign('msg', tra("Must select a category"));

		$smarty->display("error.tpl");
		die;
	}

	if (isset($_REQUEST["isValid"]) && $_REQUEST["isValid"] == 'on')
		$_REQUEST["isValid"] = 'y';
	else
		$_REQUEST["isValid"] = 'n';

	$siteId = $dirlib->dir_replace_site($_REQUEST["siteId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["url"],
		$_REQUEST["country"], $_REQUEST["isValid"]);
	$dirlib->remove_site_from_categories($siteId);

	foreach ($_REQUEST["siteCats"] as $acat) {
		$dirlib->dir_add_site_to_category($siteId, $acat);
	}

	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["url"] = '';
	$info["country"] = 'United_States';
	$info["isValid"] = 'y';
	$smarty->assign('siteId', 0);
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
if ($all) {
	$items = $dirlib->dir_list_all_sites($offset, $maxRecords, $sort_mode, $find);
} else {
	$items = $dirlib->dir_list_sites($_REQUEST["parent"], $offset, $maxRecords, $sort_mode, $find, $isValid = '');
}

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

$categs = $dirlib->dir_get_all_categories_accept_sites(0, -1, 'name asc', $find, $_REQUEST["siteId"]);
$smarty->assign('categs', $categs);

$countries = $tikilib->get_flags();
$smarty->assign_by_ref('countries', $countries);

// This page should be displayed with Directory section options
$section='directory';
include_once('tiki-section_options.php');

ask_ticket('dir-admin-sites');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-directory_admin_sites.tpl');
$smarty->display("tiki.tpl");

?>
