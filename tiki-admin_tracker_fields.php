<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_tracker_fields.php,v 1.27 2004-03-31 09:56:57 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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

$field_types = $trklib->field_types();
$smarty->assign('field_types', $field_types);

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
	$info["isSearchable"] = 'n';
	$info["isTblVisible"] = 'n';
	$info["isPublic"] = 'n';
	$info["isHidden"] = 'n';
	$info["isMandatory"] = 'n';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('options', $info["options"]);
$smarty->assign('position', $info["position"]);
$smarty->assign('isMain', $info["isMain"]);
$smarty->assign('isSearchable', $info["isSearchable"]);
$smarty->assign('isTblVisible', $info["isTblVisible"]);
$smarty->assign('isPublic', $info["isPublic"]);
$smarty->assign('isHidden', $info["isHidden"]);
$smarty->assign('isMandatory', $info["isMandatory"]);


if (isset($_REQUEST["remove"])) {
  $area = 'deltrackerfield';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$trklib->remove_tracker_field($_REQUEST["remove"],$_REQUEST["trackerId"]);
		$logslib->add_log('admintrackerfields','removed tracker field '.$_REQUEST["remove"].' from tracker '.$tracker_info['name']);
	} else {
		key_get($area);
	}
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

	if (isset($_REQUEST["isPublic"]) && $_REQUEST["isPublic"] == 'on') {
		$isPublic = 'y';
	} else {
		$isPublic = 'n';
	}

	if (isset($_REQUEST["isHidden"]) && $_REQUEST["isHidden"] == 'on') {
		$isHidden = 'y';
	} else {
		$isHidden = 'n';
	}
	
	if (isset($_REQUEST["isMandatory"]) && $_REQUEST["isMandatory"] == 'on') {
		$isMandatory = 'y';
	} else {
		$isMandatory = 'n';
	}

	//$_REQUEST["name"] = str_replace(' ', '_', $_REQUEST["name"]);
	$trklib->replace_tracker_field($_REQUEST["trackerId"], $_REQUEST["fieldId"], $_REQUEST["name"], $_REQUEST["type"], $isMain, $isSearchable,
		$isTblVisible, $isPublic, $isHidden, $isMandatory, $_REQUEST["position"], $_REQUEST["options"]);
	$logslib->add_log('admintrackerfields','changed or created tracker field '.$_REQUEST["name"].' in tracker '.$tracker_info['name']);
	$smarty->assign('fieldId', 0);
	$smarty->assign('name', '');
	$smarty->assign('type', '');
	$smarty->assign('options', '');
	$smarty->assign('isMain', $isMain);
	$smarty->assign('isSearchable', $isSearchable);
	$smarty->assign('isTblVisible', $isTblVisible);
	$smarty->assign('isPublic', $isPublic);
	$smarty->assign('isHidden', $isHidden);
	$smarty->assign('isMandatory', $isMandatory);
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
$channels = $trklib->list_tracker_fields($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $find);
$plug = array();
foreach ($channels['data'] as $c) {
	if ($c['isPublic'] == 'y') {
		$plug[] = $c['fieldId'];
	}
}
$smarty->assign('plug', implode(':',$plug));

$urlquery['find'] = $find;
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
$cant = $channels["cant"];
include "tiki-pagination.php";

$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-tracker-fields');

// Display the template
$smarty->assign('mid', 'tiki-admin_tracker_fields.tpl');
$smarty->display("tiki.tpl");

?>
