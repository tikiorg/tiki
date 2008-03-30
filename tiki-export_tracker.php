<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-export_tracker.php,v 1.12.2.10 2008-03-10 22:37:44 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

@ini_set('max_execution_time', 0); //will not work in safe_mode is on
require_once('tiki-setup.php');

if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_trackers');
	$smarty->display('error.tpl');
	die;
}
if (!isset($_REQUEST['trackerId'])) {
	$smarty->assign('msg', tra('No tracker indicated'));
	$smarty->display('error.tpl');
	die;
}
include_once('lib/trackers/trackerlib.php');

$tracker_info = $trklib->get_tracker($_REQUEST['trackerId']);
if (empty($tracker_info)) {
	$smarty->assign('msg', tra('No tracker indicated'));
	$smarty->assign('msg', tra('No tracker indicated'));
	die;
}
if ($t = $trklib->get_tracker_options($_REQUEST['trackerId'])) {
	$tracker_info = array_merge($tracker_info,$t);
}
$smarty->assign_by_ref('trackerId', $_REQUEST['trackerId']);
$smarty->assign_by_ref('tracker_info', $tracker_info);

$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);
if ($tiki_p_view_trackers != 'y') {
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display("error.tpl");
	die;
}

$filters = array();
if (!empty($_REQUEST['listfields'])) {
	$filters['fieldId'] = split(',', $_REQUEST['listfields']);
} elseif (isset($_REQUEST['which']) && $_REQUEST['which'] == 'ls') {
	$filters['or'] = array('isSearchable'=>'y', 'isTblVisible'=>'y');
} elseif (isset($_REQUEST['which']) && $_REQUEST['which'] == 'list') {
	$filters['isTblVisible'] = 'y';
}
if ($tiki_p_admin_trackers != 'y') {
	$filters['isHidden'] = array('n', 'c');
}
if ($tiki_p_tracker_view_ratings != 'y') {
	$filters['not'] = array('type'=>'s');
}
$filters['not'] = array('type'=>'h');

$fields = $trklib->list_tracker_fields($_REQUEST['trackerId'], 0, -1, 'position_asc', '', true, $filters);
$listfields = array();
foreach ($fields['data'] as $field) {
	$listfields[$field['fieldId']] = $field;
}

if (!isset($_REQUEST['which'])) {
	$_REQUEST['which'] = 'all';
}
if (!isset($_REQUEST['status'])) {
	$_REQUEST['status'] = '';
}
if (!isset($_REQUEST['initial'])) {
	$_REQUEST['initial'] = '';
}
$filterFields = '';
$values = '';
$exactValues = '';
foreach ($_REQUEST as $key =>$val) {
	if (substr($key, 0, 2) == 'f_' && !empty($val) && (!is_array($val) || !empty($val[0]))) {
		$fieldId = substr($key, 2);
		$filterFields[] = $fieldId;
		if (isset($_REQUEST["x_$fieldId"]) && $_REQUEST["x_$fieldId"] == 't' ) {
			$exactValues[] = '';
			$values[] = $val;
		} else {
			$exactValues[] = $val;
			$values[] = '';
		}
	}
}
$smarty->assign_by_ref('listfields', $listfields);

if (isset($_REQUEST['showStatus'])) {
	$showStatus = $_REQUEST['showStatus'] == 'on'?'y':'n';
} else {
	$showStatus = 'n';
}
$smarty->assign_by_ref('showStatus', $showStatus);

if (isset($_REQUEST['showItemId'])) {
	$showItemId = $_REQUEST['showItemId'] == 'on'?'y':'n';
} else {
	$showItemId = 'n';
}
$smarty->assign_by_ref('showItemId', $showItemId);

if (isset($_REQUEST['showCreated'])) {
	$showCreated = $_REQUEST['showCreated'] == 'on'?'y':'n';
} else {
	$showCreated = 'n';
}
$smarty->assign_by_ref('showCreated', $showCreated);

if (isset($_REQUEST['showLastModif'])) {
	$showLastModif = $_REQUEST['showLastModif'] == 'on'?'y':'n';
} else {
	$showLastModif = 'n';
}
$smarty->assign_by_ref('showLastModif', $showLastModif);

if (isset($_REQUEST['parse'])) {
	$parse = $_REQUEST['parse'] == 'on'?'y':'n';
} else {
	$parse = 'n';
}
$smarty->assign_by_ref('parse', $parse);

if (empty($_REQUEST['encoding'])) {
	$_REQUEST['encoding'] = 'UTF-8';
}

if (!empty($_REQUEST['file'])) {
	$fp = fopen($prefs['tmpDir'].'/'.tra('tracker')."_".$_REQUEST['trackerId'].".csv", 'w');
} else {
	header("Content-type: text/comma-separated-values; charset:".$_REQUEST['encoding']);
	header("Content-Disposition: attachment; filename=".tra('tracker')."_".$_REQUEST['trackerId'].".csv");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
}

$offset = 0;
$maxRecords = 100;
$sort_mode = 'itemId_asc';
$smarty->assign_by_ref('heading', $heading);
while (($items = $trklib->list_items($_REQUEST['trackerId'], $offset, $maxRecords, $sort_mode, $listfields, $filterFields, $values, $_REQUEST['status'], $_REQUEST['initial'], $exactValues)) && !empty($items['data'])) {
	// still need to filter the fields that are view only by the admin and the item creator
	if ($tracker_info['useRatings'] == 'y')
		foreach ($items['data'] as $f=>$v) {
			$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST['trackerId'].'.'.$items['data'][$f]['itemId'],$user);
		}
	$smarty->assign_by_ref('items', $items["data"]);

	$data = $smarty->fetch('tiki-export_tracker.tpl');
	if (!empty($_REQUEST['encoding']) && $_REQUEST['encoding'] == 'ISO-8859-1') {
		$data = utf8_decode($data);
	}

	$offset += $maxRecords;
	$heading = 'n';
	if (!empty($fp)) {
		echo $offset.' ';
		fwrite($fp, $data);
	} else
		echo $data;
}
if (!empty($fp))
	fclose($fp);
?>
