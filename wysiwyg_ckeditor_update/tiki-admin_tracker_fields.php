<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');

$access->check_feature('feature_trackers');

if (!isset($_REQUEST['trackerId'])) {
	$smarty->assign('msg', tra('No tracker indicated'));
	$smarty->display('error.tpl');
	die;
}
if ($tracker_info = $trklib->get_tracker($_REQUEST['trackerId'])) {
	if ($t = $trklib->get_tracker_options($_REQUEST['trackerId'])) {
		$tracker_info = array_merge($tracker_info, $t);
	}
} else {
	$smarty->assign('msg', tra('Incorrect param'));
	$smarty->display('error.tpl');				
	die;
}

$admin_perm = $tiki_p_admin_trackers;
if ($tiki_p_admin_trackers != 'y' && !empty($_REQUEST['trackerId'])) {
	$perms = $tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $info);
	$admin_perm = $perms['tiki_p_admin_trackers'];
}
if ($admin_perm != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You don't have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$auto_query_args = array(
	'trackerId',
	'offset',
	'sort_mode',
	'find',
	'max'
);
$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);
$field_types = $trklib->field_types();
$smarty->assign('field_types', $field_types);
if (!isset($_REQUEST["fieldId"])) {
	$_REQUEST["fieldId"] = 0;
}
$smarty->assign('fieldId', $_REQUEST["fieldId"]);
if (!isset($_REQUEST['position'])) {
	$_REQUEST['position'] = $trklib->get_last_position($_REQUEST["trackerId"]) + 10;
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
	$info["position"] = $trklib->get_last_position($_REQUEST["trackerId"]) + 10;
	$info["type"] = 't';
	$info["isMain"] = 'n';
	$info["isMultilingual"] = 'n';
	$info["isSearchable"] = 'n';
	$info["isTblVisible"] = 'n';
	$info["isPublic"] = 'n';
	$info["isHidden"] = 'n';
	$info["isMandatory"] = 'n';
	$info['description'] = '';
	$info['descriptionIsParsed'] = 'n';
	$info['errorMsg'] = '';
	$info['itemChoices'] = array();
	$info['visibleBy'] = array();
	$info['editableBy'] = array();
	$info['validation'] = $info['validationParam'] = $info['validationMessage'] = '';
}
if (isset($_REQUEST['up']) && $_REQUEST['fieldId']) {
	if (empty($_REQUEST['delta'])) $_REQUEST['delta'] = 1;
	$trklib->move_up_last_fields($_REQUEST['trackerId'], $_REQUEST["fieldId"], $_REQUEST['delta']);
	$info['position']+= $_REQUEST['delta'];
}
$smarty->assign('name', $info["name"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('options', $info["options"]);
$smarty->assign('position', $info["position"]);
$smarty->assign('isMain', $info["isMain"]);
$smarty->assign('isMultilingual', $info["isMultilingual"]);
$smarty->assign('isSearchable', $info["isSearchable"]);
$smarty->assign('isTblVisible', $info["isTblVisible"]);
$smarty->assign('isPublic', $info["isPublic"]);
$smarty->assign('isHidden', $info["isHidden"]);
$smarty->assign('isMandatory', $info["isMandatory"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('descriptionIsParsed', $info['descriptionIsParsed']);
$smarty->assign('errorMsg', $info['errorMsg']);
$smarty->assign_by_ref('itemChoices', $info['itemChoices']);
$smarty->assign_by_ref('visibleBy', $info['visibleBy']);
$smarty->assign_by_ref('editableBy', $info['editableBy']);
$smarty->assign('validation', $info['validation']);
$smarty->assign('validationParam', $info['validationParam']);
$smarty->assign('validationMessage', $info['validationMessage']);
if (isset($_REQUEST["remove"]) and ($tracker_info['useRatings'] != 'y' or $info['name'] != 'Rating')) {
	$access->check_authenticity();
	$trklib->remove_tracker_field($_REQUEST["remove"], $_REQUEST["trackerId"]);
}
function replace_tracker_from_request($tracker_info) {
	global $trklib, $logslib, $smarty;
	check_ticket('admin-tracker-fields');
	if (isset($_REQUEST["isMain"]) && ($_REQUEST["isMain"] == 'on' || $_REQUEST["isMain"] == 'y')) {
		$isMain = 'y';
	} else {
		$isMain = 'n';
	}
	if (isset($_REQUEST["isMultilingual"]) && ($_REQUEST["isMultilingual"] == 'on' || $_REQUEST["isMultilingual"] == 'y')) {
		$isMultilingual = 'y';
	} else {
		$isMultilingual = 'n';
	}
	if (isset($_REQUEST["isSearchable"]) && ($_REQUEST["isSearchable"] == 'on' || $_REQUEST["isSearchable"] == 'y')) {
		$isSearchable = 'y';
	} else {
		$isSearchable = 'n';
	}
	if (isset($_REQUEST["isTblVisible"]) && ($_REQUEST["isTblVisible"] == 'on' || $_REQUEST["isTblVisible"] == 'y')) {
		$isTblVisible = 'y';
	} else {
		$isTblVisible = 'n';
	}
	if (isset($_REQUEST["isPublic"]) && ($_REQUEST["isPublic"] == 'on' || $_REQUEST["isPublic"] == 'y')) {
		$isPublic = 'y';
	} else {
		$isPublic = 'n';
	}
	if (isset($_REQUEST["isHidden"])) {
		if ($_REQUEST["isHidden"] == 'y') {
			$isHidden = 'y';
		} elseif ($_REQUEST["isHidden"] == 'p') {
			$isHidden = 'p';
		} elseif ($_REQUEST["isHidden"] == 'c') {
			$isHidden = 'c';
		} else {
			$isHidden = 'n';
		}
	} else {
		$isHidden = 'n';
	}
	if (isset($_REQUEST["isMandatory"]) && ($_REQUEST["isMandatory"] == 'on' || $_REQUEST["isMandatory"] == 'y')) {
		$isMandatory = 'y';
	} else {
		$isMandatory = 'n';
	}
	if ($_REQUEST['type'] == 'p' && $_REQUEST['options'] == 'password') {
		$isMain = $isTblVisible = $isSearchable = 'n';
	}
	if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == 'S')) {
		if (isset($_REQUEST['descriptionStaticText'])) {
			$_REQUEST['description'] = $_REQUEST['descriptionStaticText'];
		} else {
			$_REQUEST['description'] = '';
		}
	} elseif (!isset($_REQUEST['description'])) {
		$_REQUEST['description'] = '';
	}
	if (isset($_REQUEST['descriptionIsParsed']) && ($_REQUEST['descriptionIsParsed'] == 'y' || $_REQUEST['descriptionIsParsed'] == 'on')) {
		$_REQUEST['descriptionIsParsed'] = 'y';
	} else {
		$_REQUEST['descriptionIsParsed'] = 'n';
	}
	if (!isset($_REQUEST['errorMsg'])) {
		$_REQUEST['errorMsg'] = '';
	}
	if (!isset($_REQUEST['visibleBy'])) {
		$_REQUEST['visibleBy'] = '';
	}
	if (!isset($_REQUEST['editableBy'])) {
		$_REQUEST['editableBy'] = '';
	}
	if (!isset($_REQUEST['itemChoices'])) {
		$_REQUEST['itemChoices'] = '';
	}
	//$_REQUEST["name"] = str_replace(' ', '_', $_REQUEST["name"]);
	$trklib->replace_tracker_field($_REQUEST["trackerId"], $_REQUEST["fieldId"], $_REQUEST["name"], $_REQUEST["type"], $isMain, $isSearchable, $isTblVisible, $isPublic, $isHidden, $isMandatory, $_REQUEST["position"], $_REQUEST["options"], $_REQUEST['description'], $isMultilingual, $_REQUEST["itemChoices"], $_REQUEST['errorMsg'], $_REQUEST['visibleBy'], $_REQUEST['editableBy'], $_REQUEST['descriptionIsParsed'], $_REQUEST['validation'], $_REQUEST['validationParam'], $_REQUEST['validationMessage']);
	$logslib->add_log('admintrackerfields', 'changed or created tracker field ' . $_REQUEST["name"] . ' in tracker ' . $tracker_info['name']);
	$smarty->assign('fieldId', 0);
	$smarty->assign('name', '');
	$smarty->assign('type', 't');
	$smarty->assign('options', '');
	$smarty->assign('isMain', $isMain);
	$smarty->assign('isMultilingual', $isMultilingual);
	$smarty->assign('isSearchable', $isSearchable);
	$smarty->assign('isTblVisible', $isTblVisible);
	$smarty->assign('isPublic', $isPublic);
	$smarty->assign('isHidden', $isHidden);
	$smarty->assign('isMandatory', $isMandatory);
	$smarty->assign('description', '');
	$smarty->assign('descriptionIsParsed', 'n');
	$smarty->assign('errorMsg', '');
	$smarty->assign('itemChoices', '');
	$smarty->assign('visibleBy', array());
	$smarty->assign('editableBy', array());
	$smarty->assign('position', $trklib->get_last_position($_REQUEST["trackerId"]) + 10);
	$smarty->assign('validation', '');
	$smarty->assign('validationParam', '');
	$smarty->assign('validationMessage', '');
}
if (isset($_REQUEST['refresh']) && isset($_REQUEST['exportAll'])) {
	$smarty->assign('export_all', 'y');
}
if (isset($_REQUEST['batchaction']) and $_REQUEST['batchaction'] == 'delete') {
	check_ticket('admin-tracker-fields');
	foreach($_REQUEST['action'] as $batchid) {
		$trklib->remove_tracker_field($batchid, $_REQUEST['trackerId']);
	}
}
if (isset($_REQUEST["save"])) {
	if (isset($_REQUEST['import']) and isset($_REQUEST['rawmeat'])) {
		$raw = $tikilib->read_raw($_REQUEST['rawmeat']);
		foreach($raw as $field => $value) {
			foreach($value as $it => $da) {
				$_REQUEST["$it"] = $da;
			}
			replace_tracker_from_request($tracker_info);
		}
	} else {
		replace_tracker_from_request($tracker_info);
	}
	$cookietab = 1;
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
if (isset($_REQUEST["max"])) {
	$max = $_REQUEST["max"];
} else {
	$channels = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, 0);
	if ($channels['cant'] > $maxRecords && $channels['cant'] < $maxRecords * 2) {
		// if there's a page and a half of fields show them all
		$max = $channels['cant'];
	} else {
		$max = $maxRecords;
	}
}
$smarty->assign('max', $max);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $trklib->list_tracker_fields($_REQUEST["trackerId"], $offset, $max, $sort_mode, $find, false);
$plug = array();
foreach($channels['data'] as $c) {
	if ($c['type'] == 'A' && $tracker_info['useAttachments'] != 'y') { // attachement
		$smarty->assign('error', 'This tracker does not allow attachments'); //get_strings tra('Tracker does not allow attachments')
		
	}
	if ($c['isPublic'] == 'y') {
		$plug[] = $c['fieldId'];
	}
}
$smarty->assign('plug', implode(':', $plug));
$urlquery['find'] = $find;
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
$smarty->assign_by_ref('cant', $channels['cant']);
$allGroups = $userlib->list_all_groups();
$smarty->assign_by_ref('allGroups', $allGroups);
$smarty->assign_by_ref('channels', $channels["data"]);

global $validatorslib;
require_once('lib/validatorslib.php');
$smarty->assign('validators', $validatorslib->available); 

ask_ticket('admin-tracker-fields');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_tracker_fields.tpl');
$smarty->display("tiki.tpl");
