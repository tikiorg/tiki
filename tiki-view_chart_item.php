<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_chart_item.php,v 1.14 2007-10-12 07:55:33 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/charts/chartlib.php');

//xdebug_start_profiling();
if ($prefs['feature_charts'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_charts");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['itemId'])) {
	$smarty->assign('msg', tra("No item indicated"));

	$smarty->display("error.tpl");
	die;
}

$info = $chartlib->get_chart_item($_REQUEST["itemId"]);
$chart_info = $chartlib->get_chart($info['chartId']);
$user_voted_chart = $chartlib->user_has_voted_chart($user, $info['chartId']) ? 'y' : 'n';
$user_voted_item = $chartlib->user_has_voted_item($user, $info['itemId']) ? 'y' : 'n';

if (isset($_REQUEST['vote'])) {
	if (($tiki_p_admin_charts == 'y') || (($chart_info['singleChartVotes'] == 'n' || $user_voted_chart == 'n')
		&& ($chart_info['singleItemVotes'] == 'n' || $user_voted_item == 'n'))) {
		check_ticket('view-chart-item');
		if (!isset($_REQUEST['points']))
			$_REQUEST['points'] = 0;

		$chartlib->user_vote($user, $_REQUEST['itemId'], $_REQUEST['points']);
	}

	header ("Location: tiki-view_chart.php?chartId=" . $info['chartId']);
}

$smarty->assign_by_ref('info', $info);
$smarty->assign_by_ref('chart_info', $chart_info);
$smarty->assign('chartId', $info['chartId']);
$smarty->assign('itemId', $_REQUEST['itemId']);
$smarty->assign_by_ref('user_voted_chart', $user_voted_chart);
$smarty->assign_by_ref('user_voted_item', $user_voted_item);

$info = $chartlib->get_chart_item($_REQUEST["itemId"]);
$user_voted_chart = $chartlib->user_has_voted_chart($user, $info['chartId']) ? 'y' : 'n';
$user_voted_item = $chartlib->user_has_voted_item($user, $info['itemId']) ? 'y' : 'n';

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'chartId',
	'itemId'
);

ask_ticket('view-chart-item');

$smarty->assign('mid', 'tiki-view_chart_item.tpl');
$smarty->display("tiki.tpl");

?>
