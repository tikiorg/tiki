<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/block.tikimodule.php,v 1.2 2003-12-07 14:54:51 mose Exp $
/**
 * \brief Smarty {tikimodule}{/tikimodule} block handler
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {tikimodule name="module_name" title="Module title"}
 *    <!-- module Smarty/HTML code here -->
 *  {/tikimodule}
 * \endcode
 *
 * This block may (can) use 2 Smarty templates:
 *  1) module.tpl = usual template to generate module look-n-feel
 *  2) module-error.tpl = to generate diagnostic error message about
 *     incorrect {tikimodule} parameters

\Note
error was used only in case the name was not there.
I fixed that error case. -- mose
 
 */
function smarty_block_tikimodule($params, $content, &$smarty) {
	extract($params);
	if (!isset($content))   return "";
	if (!isset($title))     $title = substr($content,0,12)."...";
	if (!isset($name))      $name  = ereg_replace("[^-_a-zA-Z0-9]","",$title);
	$smarty->assign('module_title', $title);
	$smarty->assign('module_name', $name);
	$smarty->assign_by_ref('module_content', $content);
	return $smarty->fetch('module.tpl');
}
?>
