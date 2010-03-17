<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// this script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

function wikiplugin_smarty_info() {
	return array(
		'name' => tra('Smarty function'),
		'documentation' => 'PluginSmarty',			
		'description' => tra('Provides access to the tiki smarty functions like button.'),
		'prefs' => array('wikiplugin_smarty'),
		'validate' => 'all',
		'extraparams' => true,
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Name of the smarty function'),
				'description' => 'button',
				'filter' => 'word',
			),
		),
	);
}

function wikiplugin_smarty($data, $params) {
	global $smarty;
	if (empty($params['name'])) {
		return tra('Incorrect param');
	}
	$path = 'lib/smarty_tiki/function.'.$params['name'].'.php';
	if (!file_exists($path)) {
		$path = 'lib/smarty/libs/plugins/function.'.$params['name'].'.php';
		if(!file_exists($path)){
			return tra('Incorrect param');
		}
	}
	include_once($path);
	$func = 'smarty_function_'.$params['name'];
	$content = $func($params, $smarty);
	return '~np~'.$content.'~/np~';
}
