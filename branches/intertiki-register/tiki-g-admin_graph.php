<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-g-admin_graph.php,v 1.8 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/ProcessManager.php');

$smarty->assign('is_active_help', tra('indicates if the process is active. Invalid processes cant be active'));

// The galaxia process manager PHP script.
if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
	$_REQUEST['pid'] = 0;

if ($_REQUEST["pid"]) {
	$info = $processManager->get_process($_REQUEST["pid"]);
	$info['graph'] = GALAXIA_PROCESSES."/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
	$mapfile = GALAXIA_PROCESSES."/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".map";
        if (file_exists($info['graph']) && file_exists($mapfile)) {
            $map = join('',file($mapfile));
            $url = "tiki-g-admin_activities.php?pid=".$info['pId'];
            $map = preg_replace('/href=".*?activityId/', 'href="' . $url . '&amp;activityId', $map);
            $info['map'] = $map;			
			$info['graph'] = "lib/Galaxia/processes/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
        } else {
            $info['graph'] = '';
        }
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'version' => '1.0',
		'isActive' => 'n',
		'pId' => 0
	);
}

$smarty->assign_by_ref('proc_info', $info);
$smarty->assign('pid', $_REQUEST['pid']);
$smarty->assign('info', $info);

if (isset($_REQUEST["delete"])) {
	check_ticket('g-admin-graph');
	foreach (array_keys($_REQUEST["process"])as $item) {
		$processManager->remove_process($item);
	}
}

if (isset($_REQUEST['newminor'])) {
	check_ticket('g-admin-graph');
	$processManager->new_process_version($_REQUEST['newminor']);
}

if (isset($_REQUEST['newmajor'])) {
	check_ticket('g-admin-graph');
	$processManager->new_process_version($_REQUEST['newmajor'], false);
}

$filter_name = '';
$filter_active = '';

if (isset($_REQUEST['filter'])) {
  if ($_REQUEST['filter_name']) {
    $filter_name = $_REQUEST['filter_name'];
  } 
  
  if ($_REQUEST['filter_active']) {
    $filter_active = $_REQUEST['filter_active'];
  } 
} 

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
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
$smarty->assign('filter_name', $filter_name);
$smarty->assign('filter_active', $filter_active);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$items = $processManager->list_processes($offset, $maxRecords, $sort_mode, $find, $filter_name, $filter_active);
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

if ($_REQUEST['pid']) {
    //if we are linked from, lets say tiki-g-admin_processes.php, then we get a CSRF protection error.
	//this shouldn't be?
    //check_ticket('g-admin-graph');
	$valid = $activityManager->validate_process_activities($_REQUEST['pid']);

	$errors = array();

	if (!$valid) {
		$processManager->deactivate_process($_REQUEST['pid']);

		$errors = $activityManager->get_error();
	}

	$smarty->assign('errors', $errors);
}

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'filter_name',
	'filter_active'
);

$all_procs = $items = $processManager->list_processes(0, -1, 'name_desc', '', '');
$smarty->assign_by_ref('all_procs', $all_procs['data']);
ask_ticket('g-admin-graph');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-g-admin_graph.tpl');
$smarty->display("tiki.tpl");

?>
