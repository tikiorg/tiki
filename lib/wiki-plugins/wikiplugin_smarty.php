<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_smarty_info()
{
	return array(
		'name' => tra('Smarty function'),
		'documentation' => 'PluginSmarty',
		'description' => tra('Insert a Smarty function or variable'),
		'prefs' => array('wikiplugin_smarty'),
		'validate' => 'all',
		'extraparams' => true,
		'tags' => array( 'experimental' ),
		'iconname' => 'code',
		'introduced' => 5,
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Smarty function'),
				'description' => tr('The name of the Smarty function that the button will activate. Available
					functions can be found at %0', '<code>lib/smarty/libs/plugins/function.(<strong>name</strong>).php</code>'),
				'since' => '7.0',
				'filter' => 'word',
				'default' => '',
			),
		),
	);
}

function wikiplugin_smarty($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	if (empty($params['name'])) {
		return tra('Incorrect parameter');
	}
	if($params['name'] == 'eval') {
		$content = $smarty->fetch('string:'.$params['var']);
	} else {
		$path = 'lib/smarty_tiki/function.'.$params['name'].'.php';
		if (!file_exists($path)) {
			$path = 'vendor/smarty/smarty/libs/plugins/function.'.$params['name'].'.php';
			if (!file_exists($path)) {
				return tra('Incorrect parameter');
			}
		}
		include_once($path);
		$func = 'smarty_function_'.$params['name'];
		$content = $func($params, $smarty);
	}
	return '~np~'.$content.'~/np~';
}
