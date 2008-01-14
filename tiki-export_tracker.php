<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-export_tracker.php,v 1.12.2.4 2008-01-14 18:51:02 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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
} else if (isset($_REQUEST['which']) && $_REQUEST['which'] == 'list') {
	$filters['or'] = array('isSearchable'=>'y', 'isTblVisible'=>'y');
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
	if (substr($key, 0, 2) == 'f_' && $val[0] != '') {
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

$items = $trklib->list_items($_REQUEST['trackerId'], 0, -1, $sort_mode, $listfields, $filterFields, $values, $_REQUEST['status'], $_REQUEST['initial'], $exactValues);
// still need to filter the fields that are view only by the admin and the item creator
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('item_count', $items['cant']);
$smarty->assign_by_ref('listfields', $listfields);

foreach ($items['data'] as $f=>$v) {
	$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST['trackerId'].'.'.$items['data'][$f]['itemId'],$user);
}

$data = $smarty->fetch('tiki-export_tracker.tpl');
if (!empty($_REQUEST['encoding']) && $_REQUEST['encoding'] == 'ISO-8859-1') {
	$data = utf8_decode($data);
} else {
	$_REQUEST['encoding'] = "UTF-8";
}

header("Content-type: text/comma-separated-values; charset:".$_REQUEST['encoding']);
header("Content-Disposition: attachment; filename=".tra('tracker')."_".$_REQUEST['trackerId'].".csv");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");
echo $data;
?>
