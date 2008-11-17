<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/ban/banlib.php');

if ($prefs['feature_banning'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_banning");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_banning != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['banId'])) {
	$info = $banlib->get_rule($_REQUEST['banId']);
} else {
	$_REQUEST['banId'] = 0;

	$info['sections'] = array();
	$info['title'] = '';
	$info['mode'] = 'user';
	$info['ip1'] = 255;
	$info['ip2'] = 255;
	$info['ip3'] = 255;
	$info['ip4'] = 255;
	$info['use_dates'] = 'n';
	$info['date_from'] = $tikilib->now;
	$info['date_to'] = $tikilib->now + 7 * 24 * 3600;
	$info['message'] = '';
}

$smarty->assign('banId', $_REQUEST['banId']);
$smarty->assign_by_ref('info', $info);

if (isset($_REQUEST['remove'])) {
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$banlib->remove_rule($_REQUEST['remove']);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST['del']) && isset($_REQUEST['delsec'])) {
	check_ticket('admin-banning');
	foreach (array_keys($_REQUEST['delsec'])as $sec) {
		$banlib->remove_rule($sec);
	}
}

if (isset($_REQUEST['save'])) {
	check_ticket('admin-banning');
	$_REQUEST['use_dates'] = isset($_REQUEST['use_dates']) ? 'y' : 'n';

	$_REQUEST['date_from'] = $tikilib->make_time(0, 0, 0, $_REQUEST['date_fromMonth'], $_REQUEST['date_fromDay'], $_REQUEST['date_fromYear']);
	$_REQUEST['date_to'] = $tikilib->make_time(0, 0, 0, $_REQUEST['date_toMonth'], $_REQUEST['date_toDay'], $_REQUEST['date_toYear']);
	$sections = array_keys($_REQUEST['section']);
	$banlib->replace_rule($_REQUEST['banId'], $_REQUEST['mode'], $_REQUEST['title'], $_REQUEST['ip1'], $_REQUEST['ip2'],
		$_REQUEST['ip3'], $_REQUEST['ip4'], $_REQUEST['userreg'], $_REQUEST['date_from'], $_REQUEST['date_to'], $_REQUEST['use_dates'],
		$_REQUEST['message'], $sections);

	$info['sections'] = array();
	$info['title'] = '';
	$info['mode'] = 'user';
	$info['ip1'] = 255;
	$info['ip2'] = 255;
	$info['ip3'] = 255;
	$info['ip4'] = 255;
	$info['use_dates'] = 'n';
	$info['date_from'] = $tikilib->now;
	$info['date_to'] = $tikilib->now + 7 * 24 * 3600;
	$info['message'] = '';
	$smarty->assign_by_ref('info', $info);
}

$where = '';
$wheres = array();
/*
if(isset($_REQUEST['filter'])) {
  if($_REQUEST['filter_name']) {
   $wheres[]=" name='".$_REQUEST['filter_name']."'";
  }
  if($_REQUEST['filter_active']) {
   $wheres[]=" isActive='".$_REQUEST['filter_active']."'";
  }
  $where = implode('and',$wheres);
}
*/
if (isset($_REQUEST['where'])) {
	$where = $_REQUEST['where'];
}

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

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$smarty->assign('where', $where);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$items = $banlib->list_rules($offset, $maxRecords, $sort_mode, $find, $where);
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

$smarty->assign('sections', $sections_enabled);
ask_ticket('admin-banning');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_banning.tpl');
$smarty->display("tiki.tpl");

?>
