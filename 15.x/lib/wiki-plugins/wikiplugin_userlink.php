<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_userlink_info()
{
	return array(
		'name' => tra('User Link'),
		'documentation' => 'PluginUserlink',
		'description' => tra('Display a link to a user\'s information page'),
		'prefs' => array('wikiplugin_userlink'),
		'iconname' => 'user',
		'introduced' => 6,
		'params' => array(
			'user' => array(
				'required' => false,
				'name' => tra('Username'),
				'description' => tra('User account name (which can be an email address)'),
				'since' => '6.0',
				'filter' => 'xss',
				'default' => ''
			),
		),
	);
}

function wikiplugin_userlink($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	global $user;
	$path = 'lib/smarty_tiki/modifier.userlink.php';
	include_once($path);
	$func = 'smarty_modifier_userlink';
	$content = $func(isset($params['user']) ? $params['user'] : $user, '', '', $data);
	return '~np~'.$content.'~/np~';
}
