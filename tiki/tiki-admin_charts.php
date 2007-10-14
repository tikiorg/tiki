<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_charts.php,v 1.19 2007-10-14 15:17:16 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once('tiki-setup.php');

include_once('lib/charts/chartlib.php');

if ($prefs['feature_charts'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_charts");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_charts != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['chartId']))
	$_REQUEST['chartId'] = 0;

if ($_REQUEST["chartId"]) {
	$info = $chartlib->get_chart($_REQUEST["chartId"]);
} else {
	$info = array(
		'title' => '',
		'description' => '',
		'singleItemVotes' => 'y',
		'singleChartVotes' => 'n',
		'suggestions' => 'y',
		'autoValidate' => 'n',
		'topN' => 10,
		'maxVoteValue' => 10,
		'frequency' => 0,
		'isActive' => 'y',
		'showAverage' => 'y',
		'showVotes' => 'y',
		'useCookies' => 'n',
		'lastChart' => 0,
		'voteAgainAfter' => 7,
		'created' => 0
	);
}

$smarty->assign('chartId', $_REQUEST['chartId']);
$smarty->assign('info', $info);

if (isset($_REQUEST["delete"])) {
	if (isset($_REQUEST["chart"]))
	if (is_array($_REQUEST["chart"])) {
		check_ticket('admin-charts'); 
		foreach (array_keys($_REQUEST["chart"])as $item) {
			$chartlib->remove_chart($item);
		}
	}
}

if (isset($_REQUEST['save'])) {
	check_ticket('admin-charts'); 
	$vars = array();

	$_REQUEST['singleItemVotes'] = isset($_REQUEST['singleItemVotes']) ? 'y' : 'n';
	$_REQUEST['isActive'] = isset($_REQUEST['isActive']) ? 'y' : 'n';
	$_REQUEST['singleChartVotes'] = isset($_REQUEST['singleChartVotes']) ? 'y' : 'n';
	$_REQUEST['suggestions'] = isset($_REQUEST['suggestions']) ? 'y' : 'n';
	$_REQUEST['autoValidate'] = isset($_REQUEST['autoValidate']) ? 'y' : 'n';
	$_REQUEST['showAverage'] = isset($_REQUEST['showAverage']) ? 'y' : 'n';
	$_REQUEST['showVotes'] = isset($_REQUEST['showVotes']) ? 'y' : 'n';
	$_REQUEST['useCookies'] = isset($_REQUEST['useCookies']) ? 'y' : 'n';
	$_REQUEST['lastChart'] = 0;
	$_REQUEST['created'] = $tikilib->now;
	$_REQUEST['hits'] = 0;

	foreach (array_keys($info)as $key) {
		$vars[$key] = $_REQUEST[$key];
	}

	$chartId = $chartlib->replace_chart($_REQUEST['chartId'], $vars);
	$info = array(
		'title' => '',
		'description' => '',
		'singleItemVotes' => 'y',
		'singleChartVotes' => 'n',
		'suggestions' => 'y',
		'autoValidate' => 'n',
		'isActive' => 'y',
		'topN' => 10,
		'maxVoteValue' => 10,
		'frequency' => 0,
		'showAverage' => 'y',
		'showVotes' => 'y',
		'useCookies' => 'n',
		'lastChart' => 0,
		'voteAgainAfter' => 0,
		'created' => 0
	);

	$_REQUEST['chartId'] = 0;
	$smarty->assign('chartId', 0);
	$smarty->assign('info', $info);
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
$items = $chartlib->list_charts($offset, $maxRecords, $sort_mode, $find, $where);
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

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'chartId'
);
ask_ticket('admin-charts'); 

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_charts.tpl');
$smarty->display("tiki.tpl");

?>
