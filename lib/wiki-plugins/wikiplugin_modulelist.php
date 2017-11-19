<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_modulelist_info()
{
	return [
		'name' => tra('Module List'),
		'documentation' => 'PluginModuleList',
		'description' => tra('Display the modules assigned to a zone'),
		'prefs' => [ 'wikiplugin_modulelist' ],
		'format' => 'html',
		'iconname' => 'list',
		'introduced' => 11,
		'tags' => [ 'basic' ],
		'params' => [
			'zone' => [
				'required' => true,
				'name' => tra('Zone Name'),
				'description' => tra('The name of the module zone to include. Can be a custom zone name.'),
				'since' => '11.0',
				'filter' => 'word',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Top'), 'value' => 'top'],
					['text' => tra('Top bar'), 'value' => 'topbar'],
					['text' => tra('Page top'), 'value' => 'pagetop'],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Page bottom'), 'value' => 'pagebottom'],
					['text' => tra('Bottom'), 'value' => 'bottom'],
				]
			],
		]
	];
}

function wikiplugin_modulelist($data, $params)
{
	// Pre-emptively load the modules, this normally would not happen until the final call to $smarty->display(...)
	// May have some side effects if not all of the information required to filter the modules is available at this time.
	include_once 'tiki-modules.php';

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_modulelist');

	if (! isset($params['zone'])) {
		return WikiParser_PluginOutput::argumentError(['zone']);
	}

	return smarty_function_modulelist(
		[
			'zone' => $params['zone'],
			'id' => $params['zone'] . '_plugin_modules',
		],
		$smarty
	);
}
