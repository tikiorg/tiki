<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
 *
 * It also supports the param flip="y" to make this module flippable.
 * flip="n" is the default.
 * and the param decorations="n" to suppress module decorations
 * decorations="y" is the default.

\Note
error was used only in case the name was not there.
I fixed that error case. -- mose
 
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function smarty_block_tikimodule($params, $content, &$smarty) {
	global $prefs;
	extract($params);
	if (!isset($content))   return "";
	if (!isset($error))  $error = '';
	if (!isset($overflow))  $overflow = false;
	if (!isset($title))     $title = substr(strip_tags($content),0,12). (strlen(strip_tags($content)) > 12 ? "..." : "");
	//if (!isset($name))      $name  = preg_replace("/[^-_a-zA-Z0-9]/","",$title); else $name  = preg_replace("/[^-_a-zA-Z0-9]/","",$name);
	if (!isset($name))		$name  = $title; else $name  = $name;
	$name = urlencode($name);
	if (!isset($flip) || ($flip != 'y' && $flip != 'yc')) $flip = 'n';
	if (!isset($nobox))      $nobox = 'n';
	if (!isset($notitle))      $notitle = 'n';
	if ($flip == 'yc') {
		// can be switched but initially closed
		$flip = 'y';
		$dstate = 'c';
	}
	else {
		$dstate = 'o';
	}
	if (!isset($decorations) || $decorations != 'n') $decorations = 'y';

	$smarty->assign('module_error', $error);
	$smarty->assign('module_overflow', $overflow);
	$smarty->assign('module_title', $title);
	$smarty->assign('module_name', $name);
	$smarty->assign('module_flip', $flip);
	$smarty->assign('module_dstate', $dstate);
	$smarty->assign('module_nobox', $nobox);
	$smarty->assign('module_notitle', $notitle);
	$smarty->assign('module_decorations', $decorations);
	if ( empty($type) ) $type = "module";
	$smarty->assign('module_type', $type);
	$smarty->assign_by_ref('module_content', $content);
	return $smarty->fetch('module.tpl');
}
