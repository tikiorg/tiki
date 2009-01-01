<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-g-admin_processes.php,v 1.16 2007-10-12 07:55:27 nyloth Exp $

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
	$smarty->assign('errortype', 401);
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

	$info['graph'] = "lib/Galaxia/processes/" . $info['normalized_name'] . "/graph/" . $info['normalized_name'] . ".png";
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

//Check here for an uploaded process
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	check_ticket('g-admin-processes');
	$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

	$data = '';
	$fhash = '';

	while (!feof($fp)) {
		$data .= fread($fp, 8192 * 16);
	}

	fclose ($fp);
	$size = $_FILES['userfile1']['size'];
	$name = $_FILES['userfile1']['name'];
	$type = $_FILES['userfile1']['type'];

	$process_data = $processManager->unserialize_process($data);

	if ($processManager->process_name_exists($process_data['name'], $process_data['version'])) {
		$smarty->assign('msg', tra("The process name already exists"));

		$smarty->display("error.tpl");
		die;
	} else {
		$processManager->import_process($process_data);
	}
}

if (isset($_REQUEST["delete"])) {
	if (isset($_REQUEST["process"]))
	if (is_array($_REQUEST["process"])) {
		check_ticket('g-admin-processes');
		foreach (array_keys($_REQUEST["process"])as $item) {
			$processManager->remove_process((int)$item);
		}
	}
}

if (isset($_REQUEST['newminor'])) {
	check_ticket('g-admin-processes');
	$processManager->new_process_version($_REQUEST['newminor']);
}

if (isset($_REQUEST['newmajor'])) {
	check_ticket('g-admin-processes');
	$processManager->new_process_version($_REQUEST['newmajor'], false);
}

if (isset($_REQUEST['save'])) {
	check_ticket('g-admin-processes');
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'version' => $_REQUEST['version'],
		'isActive' => 'n'
	);

	if ($processManager->process_name_exists($_REQUEST['name'], $_REQUEST['version']) && $_REQUEST['pid'] == 0) {
		$smarty->assign('msg', tra("Process already exists"));

		$smarty->display("error.tpl");
		die;
	}

	if (isset($_REQUEST['isActive']) && $_REQUEST['isActive'] == 'on') {
		$vars['isActive'] = 'y';
	}

	$pid = $processManager->replace_process($_REQUEST['pid'], $vars);

	$valid = $activityManager->validate_process_activities($pid);

	if (!$valid) {
		$processManager->deactivate_process($pid);
	}

	$info = array(
		'name' => '',
		'description' => '',
		'version' => '1.0',
		'isActive' => 'n',
		'pId' => 0
	);

	$smarty->assign('info', $info);
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
	check_ticket('g-admin-processes');
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
ask_ticket('g-admin-processes');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-g-admin_processes.tpl');
$smarty->display("tiki.tpl");

?>
