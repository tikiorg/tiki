<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-logo.php 52669 2014-09-29 20:25:00Z jyhem $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_addon_info()
{
	return array(
		'name' => tra('Addon Module'),
		'description' => tra('A module that shows content from a Tiki Addon View'),
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
			'otherparams' => array(
				'required' => false,
				'name' => tra('Other parameters'),
				'description' => tra('URL encoded string of other parameters (will not replace standard parameters)'),
				'filter' => 'text',
			)
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_addon($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');

	if (empty($module_params['package']) || empty($module_params['view'])) {
		$smarty->assign('error', tra("Please specify the name of the package and the view."));
		return;
	}

	$parts = explode('/', $module_params['package']);
	$path = TIKI_PATH . '/addons/' . $parts[0] . '_' . $parts[1] . '/views/' . $module_params['view'] . '.php';

	if (!file_exists($path)) {
		$smarty->assign('error', tra("Error: Unable to locate view file for the package."));
		return;
	}

	require_once($path);

	$functionname = "tikiaddon\\" . $parts[0] . "\\" . $parts[1] . "\\" . $module_params['view'];

	if (!function_exists($functionname)) {
		$smarty->assign('error', tra("Error: Unable to locate view file for the package."));
		return;
	}

	$prefname = 'ta_' . $parts[0] . '_' . $parts[1] . '_on';
	$folder = $parts[0] . '_' . $parts[1];
	if (!isset($GLOBALS['prefs'][$prefname]) || $GLOBALS['prefs'][$prefname] != 'y') {
		$smarty->assign('error', tra('Addon is not activated: ') . $folder);
		return;
	}

	$functionname('', $module_params);

	$smarty->assign('view', $module_params['view']);
	$smarty->assign('package', $module_params['package']);
	$smarty->assign('folder', $folder);
}
