<?php
/*
 * Created on Sep 9, 2005
 *
 * TikiWiki Calculator in a module
 * 
 * Params are:
 *  mode: default | cool
 *     default: damian's 16 button version - telephone layout
 *     cool: 27 button extended - calculator layout
 * 
 */

// the script may lead to harm if called directly, so lets keep it nice and clean
 
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$smarty->assign('mode', isset($module_params["mode"]) ? $module_params["mode"] : '');
 
?>
