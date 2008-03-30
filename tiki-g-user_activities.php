<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-g-user_activities.php,v 1.15 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/GUI.php');

if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_use_workflow != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

// When $user is null, it means an anonymous user is trying to use Galaxia
$user = is_null($user) ? "Anonymous" : $user;

// Filtering data to be received by request and used to build the where part of a query
// filter_active, filter_valid, find, sort_mode, filter_process
$where = '';
$wheres = array();

/*
if(isset($_REQUEST['filter_active'])&&$_REQUEST['filter_active']) $wheres[]="isActive='".$_REQUEST['filter_active']."'";
if(isset($_REQUEST['filter_valid'])&&$_REQUEST['filter_valid']) $wheres[]="isValid='".$_REQUEST['filter_valid']."'";
*/
if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process'])
	$wheres[] = "gp.pId=" . $_REQUEST['filter_process'] . "";

$where = implode(' and ', $wheres);

//if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'flowNum_asc';
//} else {
//	$sort_mode = $_REQUEST["sort_mode"];
//}

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

$items = $GUI->gui_list_user_activities($user, $offset, $maxRecords, $sort_mode, $find, $where);
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

if (count($processes['data']) == 1 && empty($_REQUEST['filter_process'])) {
    $_REQUEST['filter_process'] = $processes['data'][0]['pId'];
}

if (isset($_REQUEST['filter_process']) && $_REQUEST['filter_process']) {
    $actid2item = array();
    foreach (array_keys($items["data"]) as $index) {
        $actid2item[$items["data"][$index]['activityId']] = $index;
    }
    foreach ($processes['data'] as $info) {
        if ($info['pId'] == $_REQUEST['filter_process'] && !empty($info['normalized_name'])) {
            $graph = "lib/Galaxia/processes/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
            $mapfile = "lib/Galaxia/processes/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".map";
            if (file_exists($graph) && file_exists($mapfile)) {
                $maplines = file($mapfile);
                $map = '';
                foreach ($maplines as $mapline) {
                    if (!preg_match('/activityId=(\d+)/',$mapline,$matches)) continue;
                    $actid = $matches[1];
                    if (!isset($actid2item[$actid])) continue;
                    $index = $actid2item[$actid];
                    $item = $items['data'][$index];
                    if ($item['instances'] > 0) {
                        $url = "tiki-g-user_instances.php?filter_process=".$info['pId'];
                        $mapline = preg_replace('/href=".*?activityId/', 'href="' . $url . '&amp;filter_activity', $mapline);
                        $map .= $mapline;
                    } elseif ($item['isInteractive'] == 'y' && $item['type'] == 'standalone') {
                        $url = "tiki-g-run_activity.php?";
                        $mapline = preg_replace('/href=".*?activityId/', 'href="' . $url . '&amp;activityId', $mapline);
                        $map .= $mapline;
                    }
                }
                $smarty->assign('graph', $graph);
                $smarty->assign('map', $map);
                $smarty->assign('procname', $info['procname']);
            } else {
                $smarty->assign('graph', '');
            }
            break;
        }
    }
}

$section = 'workflow';
include_once ('tiki-section_options.php');
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_isInteractive',
	'filter_isAutoRouted',
	'filter_activity',
	'filter_type',
	'pid',
	'filter_process'
);
ask_ticket('g-user-activities');

$smarty->assign('mid', 'tiki-g-user_activities.tpl');
$smarty->display("tiki.tpl");

?>
