<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

global $quizlib;
if (!is_object($quizlib)) {
	require_once('lib/quizzes/quizlib.php');
}

$ranking = $quizlib->list_quizzes(0, $module_rows, 'created_desc', '');

$ranking = $quizlib->list_quiz_sum_stats(0, $module_rows, 'timesTaken_desc', '');
$smarty->assign('modTopQuizzes', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>