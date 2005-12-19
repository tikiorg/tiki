<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Parameter absurl set if the last_topics url is absolute or not [y|n].
// If not set, default = relative

global $ranklib; include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_last_topics($module_rows);
$smarty->assign('modForumsLastTopics', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
?>
