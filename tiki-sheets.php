<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-sheets.php,v 1.12 2007-10-12 07:55:32 nyloth Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');

if ($prefs['feature_sheet'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["sheetId"])) {
	$_REQUEST["sheetId"] = 0;
	$info = array();
} else {
	$info = $sheetlib->get_sheet_info($_REQUEST["sheetId"]);
	if ($tiki_p_admin == 'y' || $tiki_p_admin_sheet == 'y' || $tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet'))
		$tiki_p_view_sheet = 'y';
	else
		$tiki_p_view_sheet = 'n';
	$smarty->assign('tiki_p_view_sheet', $tiki_p_view_sheet);
	if ($tiki_p_admin == 'y' || $tiki_p_admin_sheet == 'y' || ($user && $user == $info['author']) || $tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_edit_sheet'))
		$tiki_p_edit_sheet = 'y';
	else
		$tiki_p_edit_sheet = 'n';
	$smarty->assign('tiki_p_edit_sheet', $tiki_p_edit_sheet);
	if ($tiki_p_admin == 'y' || $tiki_p_admin_sheet == 'y' || ($user && $user == $info['author']) || $tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet_history'))
		$tiki_p_view_sheet_history = 'y';
	else
		$tiki_p_view_sheet_history = 'n';
	$smarty->assign('tiki_p_view_sheet_history', $tiki_p_view_sheet_history);
}

if ($tiki_p_view_sheet != 'y') {
	$smarty->assign('msg', tra("Access Denied").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Init smarty variables to blank values
//$smarty->assign('theme','');
$smarty->assign('title', '');
$smarty->assign('description', '');
$smarty->assign('edit_mode', 'n');
$smarty->assign('chart_enabled', (function_exists('imagepng') || function_exists('pdf_new')) ? 'y' : 'n');

// If we are editing an existing gallery prepare smarty variables
if (isset($_REQUEST["edit_mode"]) && $_REQUEST["edit_mode"]) {
	if ($tiki_p_edit_sheet != 'y') {
		$smarty->assign('msg', tra("Access Denied").": feature_sheets");

		$smarty->display("error.tpl");
		die;
	}		
	check_ticket('sheet');

	// Get information about this galleryID and fill smarty variables
	$smarty->assign('edit_mode', 'y');

	if ($_REQUEST["sheetId"] > 0) {
		$smarty->assign('title', $info["title"]);
		$smarty->assign('description', $info["description"]);

		$info = $sheetlib->get_sheet_layout($_REQUEST["sheetId"]);

		$smarty->assign('className', $info["className"]);
		$smarty->assign('headerRow', $info["headerRow"]);
		$smarty->assign('footerRow', $info["footerRow"]);
	}
	else
	{
		$smarty->assign('className', 'default');
		$smarty->assign('headerRow', '0');
		$smarty->assign('footerRow', '0');
	}
}

// Process the insertion or modification of a gallery here
if (isset($_REQUEST["edit"])) {
	if ($tiki_p_edit_sheet != 'y') {
		$smarty->assign('msg', tra("Access Denied").": feature_sheets");

		$smarty->display("error.tpl");
		die;
	}		
	check_ticket('sheet');

	// Everything is ok so we proceed to edit the gallery
	$smarty->assign('edit_mode', 'y');
	//$smarty->assign_by_ref('theme',$_REQUEST["theme"]);
	$smarty->assign_by_ref('title', $_REQUEST["title"]);
	$smarty->assign_by_ref('description', $_REQUEST["description"]);

	$smarty->assign_by_ref('className', $_REQUEST["className"]);
	$smarty->assign_by_ref('headerRow', $_REQUEST["headerRow"]);
	$smarty->assign_by_ref('footerRow', $_REQUEST["footerRow"]);

	$gid = $sheetlib->replace_sheet($_REQUEST["sheetId"], $_REQUEST["title"], $_REQUEST["description"], $user );
	$sheetlib->replace_layout($gid, $_REQUEST["className"], $_REQUEST["headerRow"], $_REQUEST["footerRow"] );

	$cat_type = 'sheet';
	$cat_objid = $gid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-view_sheets.php?sheetId=" . $cat_objid;
	include_once ("categorize.php");

	$smarty->assign('edit_mode', 'n');
}

if (isset($_REQUEST["removesheet"])) {
	if ($tiki_p_edit_sheet != 'y') {

		$smarty->assign('msg', tra("Permission denied you cannot remove this sheet"));

		$smarty->display("error.tpl");
		die;
	}
  $area = 'delsheet';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$sheetlib->remove_sheet($_REQUEST["sheetId"]);
  } else {
    key_get($area);
  }
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'title_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

// Get the list of libraries available for this user (or public galleries)
// GET ALL GALLERIES SINCE ALL GALLERIES ARE BROWSEABLE
$sheets = $sheetlib->list_sheets($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($sheets["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($sheets["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('sheets', $sheets["data"]);
//print_r($galleries["data"]);
$cat_type = 'sheet';
$cat_objid = $_REQUEST["sheetId"];
include_once ("categorize_list.php");

$section = 'sheet';
include_once ('tiki-section_options.php');
ask_ticket('sheet');

// Display the template
$smarty->assign('mid', 'tiki-sheets.tpl');
$smarty->display("tiki.tpl");

?>
