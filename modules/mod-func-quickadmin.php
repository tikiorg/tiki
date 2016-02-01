<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_quickadmin_info()
{
	return array(
		'name' => tra('Quick Administration'),
		'description' => tra('Some helpful tools for administrators.'),
		'prefs' => array(),
		'params' => array(
			'mode' => array(
				'name' => tra('Mode'),
				'description' => tra('Display mode: module or header. Leave empty for module mode'),
			),
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_quickadmin($mod_reference, $module_params)
{

	TikiLib::lib('smarty')->assign('recent_prefs', TikiLib::lib('prefs')->getRecent());

}
