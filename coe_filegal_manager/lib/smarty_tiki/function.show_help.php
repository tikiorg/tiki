<?php
/*
* @author: Stéohane Casset
* @date: 06/11/2008
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_show_help($params, &$smarty)
{
	global $help_sections;

	if (sizeof($help_sections)) {
		$smarty->assign_by_ref('help_sections',$help_sections);
		return $smarty->fetch('tiki-show_help.tpl');
	}
}
