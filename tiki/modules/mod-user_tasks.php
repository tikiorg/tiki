<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/tasks/tasklib.php');

if ($user && isset($feature_tasks) && $feature_tasks == 'y' && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {
	if (isset($_SESSION['thedate'])) {
		$pdate = $_SESSION['thedate'];
	} else {
		$pdate = date("U");
	}

	if (isset($_REQUEST["modTasksDel"])) {
		foreach (array_keys($_REQUEST["modTasks"])as $task) {
			$tasklib->remove_task($user, $task);
		}
	}

	if (isset($_REQUEST["modTasksCom"])) {
		foreach (array_keys($_REQUEST["modTasks"])as $task) {
			$tasklib->complete_task($user, $task);
		}
	}

	if (isset($_REQUEST["modTasksSave"])) {
		$tasklib->replace_task($user, 0, $_REQUEST['modTasksTitle'], $_REQUEST['modTasksTitle'], date("U"), 'o', 3, 0, 0);
	}

	$ownurl =/*httpPrefix().*/ $_SERVER["REQUEST_URI"];
	$smarty->assign('ownurl', $ownurl);
	$tasks_useDates = $tikilib->get_user_preference($user, 'tasks_useDates');
	$modTasks = $tasklib->list_tasks($user, 0, 20);
	$smarty->assign('modTasks', $modTasks['data']);
}

?>
