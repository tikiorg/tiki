<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-sheets.php,v 1.6 2004-05-08 18:01:00 lphuberdeau Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');

// Now check permissions to access this page
/*
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages like this page"));
  $smarty->display("error.tpl");
  die;  
}
*/
if ($feature_sheet != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (!isset($_REQUEST["sheetId"])) {
	$_REQUEST["sheetId"] = 0;
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Check here for indivdual permissions the objectType is 'image galleries' and the id is galleryId
/*
$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["sheetId"], 'image gallery')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'image galleries');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["sheetId"], 'image gallery', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}
*/

// Init smarty variables to blank values
//$smarty->assign('theme','');
$smarty->assign('title', '');
$smarty->assign('description', '');
$smarty->assign('edit_mode', 'n');

// If we are editing an existing gallery prepare smarty variables
if (isset($_REQUEST["edit_mode"]) && $_REQUEST["edit_mode"]) {
	check_ticket('sheet');

	// Get information about this galleryID and fill smarty variables
	$smarty->assign('edit_mode', 'y');

	if ($_REQUEST["sheetId"] > 0) {
		$info = $sheetlib->get_sheet_info($_REQUEST["sheetId"]);

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
	check_ticket('sheet');
	// Saving information
	// If the user is not gallery admin
	if ($tiki_p_admin_sheet != 'y' && $tiki_p_admin != 'y') {
		if ($tiki_p_edit_sheet != 'y') {
			// If you can't create a gallery then you can't edit a gallery because you can't have a gallery
			$smarty->assign('msg', tra("Permission denied you cannot create galleries and so you cant edit them"));

			$smarty->display("error.tpl");
			die;
		}

		/* No direct permission yet
		// If the user can create a gallery then check if he can edit THIS gallery
		if ($_REQUEST["sheetId"] > 0) {
			$info = $imagegallib->get_gallery_info($_REQUEST["galleryId"]);

			if (!$user || $info["user"] != $user) {
				$smarty->assign('msg', tra("Permission denied you cannot edit this gallery"));

				$smarty->display("error.tpl");
				die;
			}
		}*/
	}

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
	if ($tiki_p_admin_sheet != 'y' && $tiki_p_admin != 'y') {

		$smarty->assign('msg', tra("Permission denied you cannot remove this gallery"));

		$smarty->display("error.tpl");
		die;
	}
  $area = 'delsheet';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$sheetlib->remove_sheet($_REQUEST["removesheet"]);
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

// If there're more records then assign next_offset
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
