<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_last_topics($module_rows);
$smarty->assign('modForumsLastTopics', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
