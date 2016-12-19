<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_user_tasks_info()
{
	return array(
		'name' => tra('My Tasks'),
		'description' => tra('Lightweight interface to user tasks, enabling to view them concisely and do some manipulations'),
		'prefs' => array("feature_tasks"),
		'params' => array(),
		'common_params' => array('nonums')
		
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_user_tasks($mod_reference, $module_params)
{
	global $user, $tiki_p_tasks;
	global $tasklib; include_once('lib/tasks/tasklib.php');
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');

	if ($user && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {
		if (isset($_REQUEST["modTasksDel"])) {
			foreach (array_keys($_REQUEST["modTasks"]) as $task) {
				$tasklib->mark_task_as_trash($task, $user);
			}
		}
	
		if (isset($_REQUEST["modTasksCom"])) {
			foreach (array_keys($_REQUEST["modTasks"]) as $task) {
				$tasklib->mark_complete_task($task, $user);
			}
		}
	
		if (isset($_REQUEST["modTasksSave"])) {
		
			$task = $tasklib->get_default_new_task($user);
			if (strlen($_REQUEST["modTasksTitle"]) > 2) {
				$tasklib->new_task($user, $user, null, null, date('U'), array('title' => $_REQUEST["modTasksTitle"]));
			} else {
				$smarty->assign('msg', tra("The task title must have at least 3 characters"));
				$smarty->display("error.tpl");
				die;
			}
		}
		$smarty->assign('ownurl', $_SERVER["REQUEST_URI"]);
		$modTasks = $tasklib->list_tasks($user, 0, -1, null, 'priority_desc', true, false, true, false);
		$smarty->assign('modTasks', $modTasks['data']);
		$smarty->clear_assign('tpl_module_title');
	}
}
