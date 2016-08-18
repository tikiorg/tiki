<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_icon_info()
{
	return array(
		'name' => tra('Icon'),
		'documentation' => 'PluginIcon',
		'description' => tra('Display an icon'),
		'prefs' => array('wikiplugin_icon'),
		'iconname' => 'information',
		'tags' => array('basic'),
		'format' => 'html',
		'introduced' => 14.1,
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra('Name of the icon'),
				'since' => '14.1',
				'filter' => 'text',
				'accepted' => tra('Valid icon name'),
				'default' => '',
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the icon (greater than 0 and less than 10).'),
				'since' => '14.1',
				'default' => 1,
				'filter' => 'digits',
				'accepted' => tra('greater than 0 and less than 10'),
				'type' => 'digits',
			),
		)
	);
}

function wikiplugin_icon($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_icon');

	return smarty_function_icon($params, $smarty);
}
