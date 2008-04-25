<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/live_support/lsadminlib.php');
include_once ('lib/live_support/lslib.php');

if ($prefs['feature_live_support'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_live_support");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_live_support_admin != 'y' && !$lsadminlib->user_is_operator($user)) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$where = '';
$wheres = array();

if (!isset($_REQUEST['filter_name']))
	$_REQUEST['filter_user'] = '';

if (!isset($_REQUEST['filter_operator']))
	$_REQUEST['filter_operator'] = '';

if (($_REQUEST['filter_user'])) {
	$wheres[] = " tiki_user='" . $_REQUEST['filter_name'] . "'";
}

if (($_REQUEST['filter_operator'])) {
	$wheres[] = " operator='" . $_REQUEST['filter_operator'] . "'";
}

$where = implode('and', $wheres);

if (isset($_REQUEST['where'])) {
	$where = $_REQUEST['where'];
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'chat_started_desc';
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
$items = $lsadminlib->list_support_requests($offset, $maxRecords, $sort_mode, $find, $where);
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

$smarty->assign('users', $lsadminlib->get_all_tiki_users());
$smarty->assign('operators', $lsadminlib->get_all_operators());

if (isset($_REQUEST['view'])) {
	$smarty->assign('events', $lsadminlib->get_events($_REQUEST['view']));
}

// Display the template
$smarty->assign('mid', 'tiki-live_support_transcripts.tpl');
$smarty->display("tiki.tpl");

?>
