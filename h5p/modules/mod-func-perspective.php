<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_perspective_info()
{
	return array(
		'name' => tra('Perspective'),
		'description' => tra('Enables to change current perspective.'),
		'prefs' => array('feature_perspective'),
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_perspective($mod_reference, $module_params)
{
	$perspectivelib = TikiLib::lib('perspective');
	$smarty = TikiLib::lib('smarty');
	global $prefs;
	
	$perspectives = $perspectivelib->list_perspectives();
	$smarty->assign('perspectives', $perspectives);

	$smarty->assign('current_perspective', $perspectivelib->get_current_perspective($prefs));
}

