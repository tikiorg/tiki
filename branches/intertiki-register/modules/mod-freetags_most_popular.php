<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $dbTiki;
global $freetaglib;
if (!isset($freetaglib) or !is_object($freetaglib)) { include_once 'lib/freetag/freetaglib.php'; }
$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, $module_rows);
$smarty->assign('most_popular_tags', $most_popular_tags);
$smarty->assign('type', isset($module_params["type"]) ? $module_params["type"] : 'list');
?>
