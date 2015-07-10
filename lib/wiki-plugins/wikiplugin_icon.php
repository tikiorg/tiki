<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_icon_info()
{
	return array(
		'name' => tra('Icon'),
		'documentation' => 'PluginIcon',
		'description' => tra('Displays an icon'),
		'prefs' => array('wikiplugin_icon'),
		'icon' => 'img/icons/grey_question.png',
		'tags' => array('basic'),
		'format' => 'html',
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra('Name of the icon'),
				'default' => '',
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the icon (1 to 9).'),
				'default' => 1,
				'type' => 'int',
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
