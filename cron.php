<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

@ignore_user_abort(TRUE); // Allow execution to continue even if the request gets canceled.

try {
	require_once 'tiki-setup.php';
} catch (Exception $e) {
	return;
}

// Check if Web Cron is enabled
$webcron_enabled = $tikilib->get_preference('webcron_enabled');

if ($webcron_enabled != 'y') {
	return;
}

// Validate if the Token to run the Web Cron matches the stored token
$cron_token = $tikilib->get_preference('webcron_token');

if (!isset($_REQUEST['token']) || $_REQUEST['token'] !== $cron_token) {
	return;
}

$asUser = 'admin';

if (TikiLib::lib('user')->user_exists($asUser)) {
	$permissionContext = new Perms_Context($asUser);
}

$tikilib = TikiLib::lib('tiki');

$last_cron_run = $tikilib->get_preference('webcron_last_run');
$cron_interval = $tikilib->get_preference('webcron_run_interval');

if (empty($cron_interval)) {
	$cron_interval = 60;
}

$start_time = time();

if ($last_cron_run + $cron_interval >= $start_time) {
	//too soon;
	return;
}

$last_cron_run = $tikilib->set_preference('webcron_last_run', $start_time);

// Get all active schedulers
$schedLib = TikiLib::lib('scheduler');
$activeSchedulers = $schedLib->get_scheduler(null, 'active');

$runTasks = array();
$reRunTasks = array();

foreach ($activeSchedulers as $scheduler) {
	// Check which tasks should run on time
	if (Scheduler_Utils::is_time_cron($start_time, $scheduler['run_time'])) {
		$runTasks[] = $scheduler;
		continue;
	}

	// Check which tasks should run if they failed previously (last execution)
	if ($scheduler['re_run']) {
		$reRunTasks[] = $scheduler;
		continue;
	}
}

foreach ($reRunTasks as $task) {

	$status = $schedLib->get_run_status($task['id']);
	if ($status == 'failed') {
		$runTasks[] = $task;
	}
}

foreach ($runTasks as $runTask) {

	$schedulerTask = new Scheduler_Item(
		$runTask['id'],
		$runTask['name'],
		$runTask['description'],
		$runTask['task'],
		$runTask['params'],
		$runTask['run_time'],
		$runTask['status'],
		$runTask['re_run']
	);

	$schedulerTask->execute();
}
