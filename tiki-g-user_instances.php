<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-g-user_instances.php,v 1.8 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/GUI.php');

// Check if feature is enabled and permissions
if ($feature_workflow != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_use_workflow != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('tiki_p_abort_instance',$tiki_p_abort_instance);
$smarty->assign('tiki_p_exception',$tiki_p_exception_instance);

// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process
if (isset($_REQUEST['send'])) {
	check_ticket('g-user-instances');
	$GUI->gui_send_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['abort'])) {
    check_ticket('g-user-instances');
    if ($tiki_p_abort_instance != 'y') {
        $smarty->assign('msg', tra("You couldn't abort a instance"));

        $smarty->display("error.tpl");
        die;
    }
    $GUI->gui_abort_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['exception'])) {
    check_ticket('g-user-instances');
    if ($tiki_p_exception_instance != 'y') {
        $smarty->assign('msg', tra("You couldn't exception a instance"));

        $smarty->display("error.tpl");
        die;
    }
    $GUI->gui_exception_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['grab'])) {
	check_ticket('g-user-instances');
	$GUI->gui_grab_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
}

if (isset($_REQUEST['release'])) {
	check_ticket('g-user-instances');
	$GUI->gui_release_instance($user, $_REQUEST['aid'], $_REQUEST['iid']);
}

$where = '';
$wheres = array();

if (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'])
	$wheres[] = "gi.status='" . $_REQUEST['filter_status'] . "'";

if (isset($_REQUEST['filter_act_status']) && $_REQUEST['filter_act_status'])
	$wheres[] = "actstatus='" . $_REQUEST['filter_act_status'] . "'";

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gi.pId=" . $_REQUEST['filter_process'] . "";

if (isset($_REQUEST['filter_activity']) && $_REQUEST['filter_activity'])
	$wheres[] = "gia.activityId=" . $_REQUEST['filter_activity'] . "";

if (isset($_REQUEST['filter_user']) && $_REQUEST['filter_user'])
	$wheres[] = "user='" . $_REQUEST['filter_user'] . "'";

if (isset($_REQUEST['filter_owner']) && $_REQUEST['filter_owner'])
	$wheres[] = "owner='" . $_REQUEST['filter_owner'] . "'";

$where = implode(' and ', $wheres);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'procname_asc';
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
$smarty->assign('where', $where);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$items = $GUI->gui_list_user_instances($user, $offset, $maxRecords, $sort_mode, $find, $where);
$smarty->assign('cant', $items['cant']);

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

$processes = $GUI->gui_list_user_processes($user, 0, -1, 'procname_asc', '', '');
$smarty->assign_by_ref('all_procs', $processes['data']);

$all_statuses = array(
	'aborted',
	'active',
	'exception'
);

$smarty->assign('statuses', $all_statuses);

$section = 'workflow';
include_once ('tiki-section_options.php');

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_user',
	'filter_status',
	'filter_act_status',
	'filter_type',
	'pid',
	'filter_process',
	'filter_owner',
	'filter_activity'
);
ask_ticket('g-user-instances');

$smarty->assign('mid', 'tiki-g-user_instances.tpl');
$smarty->display("tiki.tpl");

?>
