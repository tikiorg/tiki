<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_modulelist_info()
{
	return array(
		'name' => tra('Module List'),
		'documentation' => 'PluginModuleList',
		'description' => tra('Display the modules assigned to a zone'),
		'prefs' => array( 'wikiplugin_modulelist' ),
		'format' => 'html',
		'iconname' => 'list',
		'introduced' => 11,
		'tags' => array( 'basic' ),
		'params' => array(
			'zone' => array(
				'required' => true,
				'name' => tra('Zone Name'),
				'description' => tra('The name of the module zone to include. Can be a custom zone name.'),
				'since' => '11.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Top'), 'value' => 'top'),
					array('text' => tra('Top bar'), 'value' => 'topbar'),
					array('text' => tra('Page top'), 'value' => 'pagetop'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Page bottom'), 'value' => 'pagebottom'),
					array('text' => tra('Bottom'), 'value' => 'bottom'),
				)
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

	if (! isset($params['zone'])) {
		return WikiParser_PluginOutput::argumentError(array('zone'));
	}

	return smarty_function_modulelist(
		array(
			'zone' => $params['zone'],
			'id' => $params['zone'] . '_plugin_modules',
		),
		$smarty
	);
}
