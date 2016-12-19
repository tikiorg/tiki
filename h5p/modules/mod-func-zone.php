<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-mustread.php 52093 2014-07-23 19:11:54Z lphuberdeau $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_zone_info()
{
	return array(
		'name' => tr('Module Zone'),
		'description' => tr('Can contain other modules so can be used as a Bootstrap navbar object, for example.'),
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tr('Zone Name'),
				'description' => tr('Must be unique; the zone becomes an "extra module zone" and will appear in the admin modules panel.'),
				'filter' => 'text',
				'default' => '',
			),
			'zoneclass' => array(
				'required' => false,
				'name' => tr('CSS Class'),
				'description' => tr('Example for a Bootstrap "social" navbar:') . ' "navbar navbar-inverse navbar-fixed-top"',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_zone($mod_reference, $module_params)
{
	global $prefs;

	$modlib = TikiLib::lib('mod');

	if (! array_key_exists($module_params['name'], $modlib->module_zones)) {
		if (! in_array($module_params['name'], array_filter((array) $prefs['module_zone_available_extra']))) {
			$prefs['module_zone_available_extra'][] = $module_params['name'];
			TikiLib::lib('tiki')->set_preference('module_zone_available_extra', $prefs['module_zone_available_extra']);
			$modlib = new ModLib();
		}
	}

}
