<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_tracker_fields.php,v 1.17 2004-01-04 19:47:00 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");

	$smarty->display("error.tpl");
	die;
}

// To admin tracker fields the user must have permission to admin trackers
if ($tiki_p_admin_trackers != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}	

if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);

if (!isset($_REQUEST["fieldId"])) {
	$_REQUEST["fieldId"] = 0;
}

$smarty->assign('fieldId', $_REQUEST["fieldId"]);

if (!isset($_REQUEST['position'])) {
	$_REQUEST['position'] = $trklib->get_last_position($_REQUEST["trackerId"])+1;
}

if (!isset($_REQUEST['options'])) {
	$_REQUEST['options'] = '';
}

if ($_REQUEST["fieldId"]) {
	$info = $trklib->get_tracker_field($_REQUEST["fieldId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["options"] = '';
	$info["position"] = $trklib->get_last_position($_REQUEST["trackerId"])+1;
	$info["type"] = 'o';
	$info["isMain"] = 'n';
	$info["isSearchable"] = 'y';
	$info["isTblVisible"] = 'y';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('options', $info["options"]);
$smarty->assign('position', $info["position"]);
$smarty->assign('isMain', $info["isMain"]);
$smarty->assign('isSearchable', $info["isSearchable"]);
$smarty->assign('isTblVisible', $info["isTblVisible"]);


if (isset($_REQUEST["remove"])) {
	check_ticket('admin-tracker-fields');
	$trklib->remove_tracker_field($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-tracker-fields');
	if (isset($_REQUEST["isMain"]) && $_REQUEST["isMain"] == 'on') {
		$isMain = 'y';
	} else {
		$isMain = 'n';
	}

	if (isset($_REQUEST["isSearchable"]) && $_REQUEST["isSearchable"] == 'on') {
		$isSearchable = 'y';
	} else {
		$isSearchable = 'n';
	}

	if (isset($_REQUEST["isTblVisible"]) && $_REQUEST["isTblVisible"] == 'on') {
		$isTblVisible = 'y';
	} else {
		$isTblVisible = 'n';
	}

	//$_REQUEST["name"] = str_replace(' ', '_', $_REQUEST["name"]);
	$trklib->replace_tracker_field($_REQUEST["trackerId"], $_REQUEST["fieldId"], $_REQUEST["name"], $_REQUEST["type"], $isMain, $isSearchable,
		$isTblVisible, $_REQUEST["position"], $_REQUEST["options"]);
	$smarty->assign('fieldId', 0);
	$smarty->assign('name', '');
	$smarty->assign('type', '');
	$smarty->assign('options', '');
	$smarty->assign('isMain', $isMain);
	$smarty->assign('isSearchable', $isSearchable);
	$smarty->assign('isTblVisible', $isTblVisible);
	$smarty->assign('position', $trklib->get_last_position($_REQUEST["trackerId"])+1);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'position_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, $sort_mode, $find);
$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-tracker-fields');

// Display the template
$smarty->assign('mid', 'tiki-admin_tracker_fields.tpl');
$smarty->display("tiki.tpl");

?>
