<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-directory_validate_sites.php,v 1.18 2007-10-12 07:55:26 nyloth Exp $

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

if ($tiki_p_validate_links != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["validate"]) && isset($_REQUEST['sites'])) {
	check_ticket('dir-validate');
	foreach (array_keys($_REQUEST["sites"])as $siteId) {
		$dirlib->dir_validate_site($siteId);
	}
}

if (isset($_REQUEST["remove"])) {
  $area = 'deldirvalidate';
  if ($prefs['feature_ticketlib2'] != 'y' or ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])))) {
    key_check($area);
		$dirlib->dir_remove_site($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}
if (isset($_REQUEST["del"]) && isset($_REQUEST['sites'])) {
	check_ticket('dir-validate');
	foreach (array_keys($_REQUEST["sites"])as $siteId) {
		$dirlib->dir_remove_site($siteId);
	}
}

// Listing: invalid sites
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
$items = $dirlib->dir_list_invalid_sites($offset, $maxRecords, $sort_mode, $find);
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

/*
$categs = $dirlib->dir_get_all_categories_np(0,-1,'name asc',$find,$_REQUEST["parent"]);
$smarty->assign('categs',$categs);
$all_categs = $dirlib->dir_get_all_categories(0,-1,'name asc',$find);
$smarty->assign('all_categs',$all_categs);
*/

// This page should be displayed with Directory section options
$section='directory';
include_once('tiki-section_options.php');
ask_ticket('dir-validate');

// Display the template
$smarty->assign('mid', 'tiki-directory_validate_sites.tpl');
$smarty->display("tiki.tpl");

?>
