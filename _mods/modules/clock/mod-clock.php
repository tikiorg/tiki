<?php
/*
 * Created on Sep 9, 2005
 *
 * TikiWiki Clock in a module
 * 
 * Params are:
 *  mode: default | world
 *     default: damian's 1-row and status bar version
 *     world - an anlog and digital world clock (doesn't support DST)
 * 
 */

// the script may lead to harm if called directly, so lets keep it nice and clean
 
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$smarty->assign('mode', isset($module_params["mode"]) ? $module_params["mode"] : '');
 
?>
