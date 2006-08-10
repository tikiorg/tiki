<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

/**
 * Show a help text
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function smarty_block_ws_help($params, $content, &$smarty) {
	global $user_flip_modules;
	extract($params);
	if (!isset($content))   return "error";

	$smarty->assign('param1', "param");
	$smarty->assign_by_ref('help_content', $content);
	return $smarty->fetch('tiki-workspaces_help.tpl');

}
?>
