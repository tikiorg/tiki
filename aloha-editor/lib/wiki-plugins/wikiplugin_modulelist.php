<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_module.php 43760 2012-11-02 14:57:56Z lphuberdeau $

function wikiplugin_modulelist_info()
{
	return array(
		'name' => tra('Module List'),
		'documentation' => 'PluginModuleList',
		'description' => tra('Displays a list of modules as configured by the administrator. Allows to include multiple modules in a page with various conditionals without relying excessively on group plugins or other techniques.'),
		'prefs' => array( 'wikiplugin_modulelist' ),
		'format' => 'html',
		'icon' => 'img/icons/module.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'zone' => array(
				'required' => true,
				'name' => tra('Zone Name'),
				'description' => tra('The name of the module zone to include. Can be a custom zone name.'),
				'default' => '',
			),
		)
	);
}

function wikiplugin_modulelist($data, $params)
{
	// Pre-emptively load the modules, this normally would not happen until the final call to $smarty->display(...)
	// May have some side effects if not all of the information required to filter the modules is available at this time.
	include_once 'tiki-modules.php';

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_modulelist');

	return smarty_function_modulelist(array(
		'zone' => $params['zone'],
		'id' => $params['zone'] . '_plugin_modules',
	), $smarty);
}
