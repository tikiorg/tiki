<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
function module_mobile_info()
{
	return array(
		'name' => tra('Mobile'),
		'description' => tra('Currently only shows switch to and from mobile mode.'),
		'prefs' => array('mobile_feature'),
		'params' => array(
			'to_label' => array(
				'name' => tra('To Label'),
				'description' => tra('Switch to normal site label'),
			),
			'from_label' => array(
				'name' => tra('From Label'),
				'description' => tra('Switch to mobile site label'),
			),
			'switch_perspective' => array(
				'name' => tra('Switch Perspective'),
				'description' => tra('Also switch perspective back to this one when leaving mobile mode.'),
			),
			'stay_on_same_page' => array(
				'name' => tra('Stay'),
				'description' => tra('Stay on same page.') . ' (1/0)',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_mobile($mod_reference, &$module_params)
{
	$module_params['to_label']  = !isset($module_params['to_label']) ? tra('Switch to mobile site') : $module_params['to_label'];
	$module_params['from_label']  = !isset($module_params['from_label']) ? tra('Switch to normal site') : $module_params['from_label'];
	$module_params['stay_on_same_page']  = !isset($module_params['stay_on_same_page']) ? 1 : $module_params['stay_on_same_page'];
}

