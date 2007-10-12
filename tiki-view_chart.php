<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_chart.php,v 1.19 2007-10-12 07:55:33 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/charts/chartlib.php');

if ($prefs['feature_charts'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_charts");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_chart != 'y') {
        $smarty->assign('msg', tra("You do not have permission to use this feature"));

        $smarty->display("error.tpl");
        die;
}

if (!isset($_REQUEST['chartId'])) {
	$smarty->assign('msg', tra("No chart indicated"));

	$smarty->display("error.tpl");
	die;
}

$chart_info = $chartlib->get_chart($_REQUEST["chartId"]);
$smarty->assign_by_ref('chart_info', $chart_info);

$smarty->assign('next_chart', $chart_info['lastChart'] + ($chart_info['frequency']));

// Regenerate the ranking if no ranking is found or if
// the last ranking is too old for the frequency
$chartlib->generate_new_ranking($chart_info['chartId']);

// If no period indicated then period is last
// Note that there's always at least one period because the ranking is
// generated if not existed
if (!isset($_REQUEST['period'])) {
	$_REQUEST['period'] = $chartlib->get_last_period($_REQUEST['chartId']);
}

// If the chart is not realtime then build links to the
// next and previous periods if they exist
if ($chart_info['frequency']) {
	$lastPeriod = $chartlib->get_last_period($chart_info['chartId']);

	$firstPeriod = $chartlib->get_first_period($chart_info['chartId']);

	if ($firstPeriod && $firstPeriod < $_REQUEST['period']) {
		$smarty->assign('prevPeriod', $_REQUEST['period'] - 1);
	} else {
		$smarty->assign('prevPeriod', 0);
	}

	if ($lastPeriod && $lastPeriod > $_REQUEST['period']) {
		$smarty->assign('nextPeriod', $_REQUEST['period'] + 1);
	} else {
		$smarty->assign('nextPeriod', 0);
	}
}

$chartlib->add_chart_hit($chart_info['chartId']);

// Purge user votes that are too old using voteagainafter
$chartlib->purge_user_votes($chart_info['chartId'], $chart_info['voteAgainAfter']);

// determine if the user has voted this chart or not
$user_voted_chart = $chartlib->user_has_voted_chart($user, $chart_info['chartId']);
$smarty->assign('user_voted_chart', $user_voted_chart ? 'y' : 'n');

// now get the ranking items
$items = $chartlib->get_ranking($chart_info['chartId'], $_REQUEST['period']);
$smarty->assign_by_ref('items', $items);
$smarty->assign('max_dif', $chartlib->max_dif($chart_info['chartId'], $_REQUEST['period']));

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'chartId'
);

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';


$all_items = $chartlib->list_chart_items(0, -1, 'title_asc', $_REQUEST['find'], "chartId", $chart_info['chartId']);
$smarty->assign_by_ref('all_items', $all_items['data']);

ask_ticket('view-chart');

$smarty->assign('mid', 'tiki-view_chart.tpl');
$smarty->display("tiki.tpl");

?>
