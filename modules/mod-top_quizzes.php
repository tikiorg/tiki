<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$ranking = $tikilib->list_quizzes(0, $module_rows, 'created_desc', '');

$ranking = $tikilib->list_quiz_sum_stats(0, $module_rows, 'timesTaken_desc', '');
$smarty->assign('modTopQuizzes', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
