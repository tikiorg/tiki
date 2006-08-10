<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

/**
 * Workspace modules 
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {tiki_workspaces_module name="module_name" title="Module title"}
 *    <!-- module Smarty/HTML code here -->
 *  {/tiki_workspaces_module}
 * \endcode
 *
 *
 * It also supports the param flip="y" to make this module flippable.
 * flip="n" is the default.
 * and the param decorations="n" to suppress module decorations
 * decorations="y" is the default.
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function smarty_block_tiki_workspaces_module($params, $content, &$smarty) {
	global $user_flip_modules;
	extract($params);
	if (!isset($content))   return "";
	if (!isset($title))     $title = substr($content,0,12)."...";
	if (!isset($name))      $name  = ereg_replace("[^-_a-zA-Z0-9]","",$title);
	if (!isset($flip) || $flip != 'y') $flip = 'n';
	if (!isset($decorations) || $decorations != 'n') $decorations = 'y';

        if (isset($user_flip_modules) && ($user_flip_modules != 'module')) {
	    $flip = $user_flip_modules;
	}

if ($decorations == 'y') {	
	$smarty->assign('module_title', $title);
	$smarty->assign('module_style_title', $style_title);
	$smarty->assign('module_style_data', $style_data);
	$smarty->assign('module_name', $name);
	$smarty->assign('module_flip', $flip);
	$smarty->assign('module_decorations', $decorations);
	$smarty->assign_by_ref('module_content', $content);
	return $smarty->fetch('tiki-workspaces_module.tpl');
} else {
	return $content.$module_error;
}
}
?>
