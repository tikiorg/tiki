<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_autoqueryargs_info()
{
	return array(
		'name' => tra('Auto Query Args'),
		'documentation' => 'PluginAutoQueryArgs',
		'description' => tra('Automatically propagate arguments when clicking on links'),
		'prefs' => array('wikiplugin_autoqueryargs'),
		'iconname' => 'link-external',
		'introduced' => 12,
		'params' => array(
			'arguments' => array(
				'required' => false,
				'name' => tra('Arguments for auto query'),
				'description' => tra('Colon-separated list of arguments, the values of which will be propagated through
					any link created below this plugin'),
				'since' => '12.0',
				'filter' => 'text',
				'separator' => ':',
				'default' => ''
			),
		),
	);
}

function wikiplugin_autoqueryargs($data, $params)
{
	global $user;
	$arguments = $params['arguments'];
	if ( count($arguments) > 0 && is_array($arguments) ) {
		global $auto_query_args;
		$auto_query_args = empty($auto_query_args)? $arguments: array_merge($auto_query_args, $arguments);
	}

}
