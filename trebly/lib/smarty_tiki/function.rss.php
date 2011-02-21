<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* inserts the content of an rss feed into a module */
function smarty_function_rss($params, &$smarty)
{
	global $tikilib;
	global $dbTiki;
	global $rsslib;
	include_once('lib/rss/rsslib.php');
	extract($params, EXTR_SKIP);
	// Param = zone
	if(empty($id)) {
		$smarty->trigger_error("assign: missing id parameter");
		return;
	}
	if(empty($max)) {
		$max = 99;
	}

	global $tikilib;
	return $tikilib->plugin_execute( 'rss', '', array(
		'id' => $id,
		'max' => $max,
	), 0, false, array( 'context_format' => 'html' ) );
}
