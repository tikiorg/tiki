<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $user, $tasklib;
include_once('lib/tasks/tasklib.php');

if ($user && isset($prefs['feature_tasks']) && $prefs['feature_tasks'] == 'y' && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {
	if (isset($_SESSION['thedate'])) {
		$pdate = $_SESSION['thedate'];
	} else {
		$pdate = date("U");
	}

	if (isset($_REQUEST["modTasksDel"])) {
		foreach (array_keys($_REQUEST["modTasks"])as $task) {
			$tasklib->mark_task_as_trash($task, $user);
		}
	}

	if (isset($_REQUEST["modTasksCom"])) {
		foreach (array_keys($_REQUEST["modTasks"])as $task) {
			$tasklib->mark_complete_task($task, $user);
		}
	}

	if (isset($_REQUEST["modTasksSave"])) {
	
		$task = $tasklib->get_default_new_task($user);
		if(strlen($_REQUEST["modTasksTitle"]) > 2) {
			$tasklib->new_task($user, $user, null, null, date('U'), array('title' => $_REQUEST["modTasksTitle"]));
		} else {
			$smarty->assign('msg', tra("The task title must have at least 3 characters"));
			$smarty->display("error.tpl");
			die;
		}
	}
    $smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["SCRIPT_NAME"]."?".urlencode($_SERVER["QUERY_STRING"]));
	$modTasks = $tasklib->list_tasks($user, 0, -1, null, 'priority_desc', true, false, true, false);
	$smarty->assign('modTasks', $modTasks['data']);
}

?>
