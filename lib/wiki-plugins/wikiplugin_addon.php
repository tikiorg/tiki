<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),
		'params' => array(
			'package' => array(
				'required' => true,
				'name' => tra('Name of package (vendor/name)'),
				'description' => tra('Name of package in the form vendor/name'),
				'filter' => 'text',
			),
			'view' => array(
				'required' => true,
				'name' => tra('Name of the view'),
				'description' => tra('Name of the view file without the .php'),
				'filter' => 'text',
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
	if (!isset($GLOBALS['prefs'][$prefname]) || $GLOBALS['prefs'][$prefname] != 'y') {
		return tra('Addon is not activated: ') . $folder;
	}

	return "~np~" . $functionname($data, $params) . "~/np~";
}
