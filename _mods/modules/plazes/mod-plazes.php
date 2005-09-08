<?php
/*
 * Created on Sep 8, 2005
 *
 * Damian Parker - TikiWiki Plazes module for left/right assignment
 * 
 * Params are:
 *  key: your plazes key
 *  map: show a map 1 = yes, 0 = no
 * 
 */

// the script may lead to harm if called directly, so lets keep it nice and clean
 
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$smarty->assign('key', isset($module_params["key"]) ? $module_params["key"] : '');
$smarty->assign('map', isset($module_params["map"]) ? $module_params["map"] : '');
 
?>
