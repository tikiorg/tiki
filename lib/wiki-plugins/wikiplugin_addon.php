<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addon_info()
{
	return array(
		'name' => tra('Tiki Addon View'),
		'documentation' => 'PluginAddon',
		'description' => tra('Display output of a Tiki Addon View'),
		'prefs' => array('wikiplugin_addon'),
		'body' => '',
		'introduced' => 14,
		'filter' => 'rawhtml_unsafe',
		'iconname' => 'view',
		'tags' => array( 'basic' ),
		'extraparams' => true,
		'params' => array(
			'package' => array(
				'required' => true,
				'name' => tra('Package Name'),
				'description' => tr('Name of package in the form %0vendor/name%1', '<code>', '</code>'),
				'filter' => 'text',
				'since' => '14.0',
			),
			'view' => array(
				'required' => true,
				'name' => tra('Name of the view'),
				'description' => tr('Name of the view file without the %0.php%1', '<code>', '</code>'),
				'filter' => 'text',
				'since' => '14.0',
			),
		),
	);
}

function wikiplugin_addon($data, $params)
{
	if (empty($params['package']) || empty($params['view'])) {
		return tra("Please specify the name of the package and the view.");
	}

	$parts = explode('/', $params['package']);
	$path = TIKI_PATH . '/addons/' . $parts[0] . '_' . $parts[1] . '/views/' . $params['view'] . '.php';

	if (!file_exists($path)) {
		return tra("Error: Unable to locate view file for the package.");
	}

	require_once($path);

	$functionname = "tikiaddon\\" . $parts[0] . "\\" . $parts[1] . "\\" . $params['view'];

	if (!function_exists($functionname)) {
		return tra("Error: Unable to locate function name for the view.");
	}

	$prefname = 'ta_' . $parts[0] . '_' . $parts[1] . '_on';
	$folder = $parts[0] . '_' . $parts[1];
	if (!isset($GLOBALS['prefs'][$prefname]) || $GLOBALS['prefs'][$prefname] != 'y') {
		return tra('Addon is not activated: ') . $folder;
	}

	return "~np~" . $functionname($data, $params) . "~/np~";
}
