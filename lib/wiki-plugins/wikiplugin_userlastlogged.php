<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_userlastlogged_info()
{
	return array(
		'name' => tra('Last Login information'),
		'documentation' => tra('PluginUserLastLogged'),
		'description' => tra('Show the last login information for a given or current user'),
		'iconname' => 'user',
		'introduced' => 13,
		'params' => array(
			'user' => array(
				'required' => false,
				'name' => tra('Username'),
				'description' => tra('Username to display last login information for. Current user information shown
					if left blank.'),
				'since' => '13.0',
				'filter' => 'username',
			),
		),
	);
}

function wikiplugin_userlastlogged($data, $params)
{
	global $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	
	if (!empty($params['user'])) {
		$info = $userlib->get_user_info($params['user']);
	} else {
		$info = $userlib->get_user_info($user);
	}
	
	return $tikilib->get_short_datetime($info['lastLogin']);
}
