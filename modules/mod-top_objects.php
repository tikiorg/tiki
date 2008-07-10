<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $statslib; include_once ('lib/stats/statslib.php');

$best_objects_stats = $statslib->best_overall_object_stats($module_rows);

$smarty->assign('modTopObjects', $best_objects_stats);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('module_rows', $module_rows);
?>