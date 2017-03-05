<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function saveScheduler($isUpdate = false)
{

	$addTask = true;
	$errors = [];

	$name = $_POST['scheduler_name'];
	$description = $_POST['scheduler_description'];
	$task = $_POST['scheduler_task'];
	$params = $_POST['scheduler_params'];
	$runTime = $_POST['scheduler_time'];
	$status = $_POST['scheduler_status'];
	$reRun = $_POST['scheduler_rerun'] == 'on' ? 1 : 0;

	if (empty($name)) {
		$errors[] = tra('Scheduler Task name cannot be empty');
		$addTask = false;
	}

	//@todo improve this validation
	if (empty($task)) {
		$errors[] = tra('Scheduler Task task is invalid');
		$addTask = false;
	}

	if (empty($runTime)) {
		$errors[] = tra('Scheduler Task time cannot be empty');
		$addTask = false;
	}

	//@todo add validation task
//	if (isValid($runTime)) {
//		$errors[] = tra('Scheduler Task time is invalid');
//		$addTask = false;
//	}

	if (empty('status')) {
		$errors[] = tra('Scheduler Task status cannot be empty');
		$addTask = false;
	}

	$schedLib = TikiLib::lib('scheduler');

	if ($addTask) {

		if (!$isUpdate) {
			$schedLib->set_scheduler($name, $description, $task, $params, $runTime, $status, $reRun);
			$feedback = sprintf(tra('Scheduler %s was created.'), $name);
		} else {
			$schedLib->set_scheduler($name, $description, $task, $params, $runTime, $status, $reRun, $_POST['scheduler']);
			$feedback = sprintf(tra('Scheduler %s was updated.'), $name);
		}

		Feedback::success($feedback, 'session');

	} else {
		if (!empty($errors)) {
			Feedback::error(['mes' => $errors], 'session');
			$access = TikiLib::lib('access');
			$access->redirect('tiki-admin_schedulers.php');
			die;
		}
	}
}

require_once('tiki-setup.php');

$access = TikiLib::lib('access');
$access->check_permission(array('tiki_p_admin_schedulers'));

$auto_query_args = array();

$schedLib = TikiLib::lib('scheduler');

if (isset($_POST['new_scheduler'])) {

	// Add a new scheduler tasks
	$addTask = true;
	saveScheduler(false);
	$cookietab = 1;

} else if (isset($_POST['editscheduler']) and isset($_POST['scheduler'])) {
	saveScheduler(true);
	$cookietab = '1';

} else if (isset($_REQUEST['scheduler']) and $_REQUEST['scheduler']) {

	$schedulerinfo = $schedLib->get_scheduler($_REQUEST['scheduler']);

	if (empty($schedulerinfo)) {
		$schedulerinfo['name'] = '';
		$schedulerinfo['description'] = '';
		$schedulerinfo['task'] = '';
		$schedulerinfo['run_time'] = '';
		$schedulerinfo['status'] = '';
		$schedulerinfo['re_run'] = '';

		$_REQUEST['scheduler'] = 0;
	} else {

		$schedulerRuns = $schedLib->get_scheduler_runs($_REQUEST['scheduler'], 10);

	}

	if (isset($_REQUEST['logs'])) {
		$cookietab = '3';
	} else {
		$cookietab = '2';
	}

} else {
	$schedulerinfo['name'] = '';
	$schedulerinfo['description'] = '';
	$schedulerinfo['task'] = '';
	$schedulerinfo['run_time'] = '';
	$schedulerinfo['status'] = '';
	$schedulerinfo['re_run'] = '';

	$_REQUEST['scheduler'] = 0;
}

$tasks = $schedLib->get_scheduler();
$smarty->assign_by_ref('schedulers', $tasks);

if (isset($_REQUEST['add'])) {
	$cookietab = '2';
}

$smarty->assign('schedulerinfo', $schedulerinfo);
$smarty->assign('schedulerruns', isset($schedulerRuns) ? $schedulerRuns : array());
$smarty->assign('schedulerId', $_REQUEST['scheduler']);
$smarty->assign('schedulerTasks', Scheduler_Item::getAvailableTasks());
$smarty->assign('selectedTask', '');
$smarty->assign('schedulerStatus', array(
	Scheduler_Item::STATUS_ACTIVE => tra('Active'),
	Scheduler_Item::STATUS_INACTIVE => tra('Inactive'),
));

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_schedulers.tpl');
$smarty->display('tiki.tpl');
