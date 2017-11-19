<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_userlastlogged_info()
{
	return [
		'name' => tra('Last Login information'),
		'documentation' => tra('PluginUserLastLogged'),
		'description' => tra('Show the last login information for a given or current user'),
		'iconname' => 'user',
		'introduced' => 13,
		'params' => [
			'user' => [
				'required' => false,
				'name' => tra('Username'),
				'description' => tra('Username to display last login information for. Current user information shown
					if left blank.'),
				'since' => '13.0',
				'filter' => 'username',
			],
			'date_format' => [
				'required' => false,
				'name' => tra('DateFormat'),
				'description' => tra('Date format setting. Short_datetime used by default'),
				'since' => '16.0',
				'filter' => 'dateformat',
			],
		],
	];
}

function wikiplugin_userlastlogged($data, $params)
{
	global $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');

	if (! empty($params['user'])) {
		$info = $userlib->get_user_info($params['user']);
	} else {
		$info = $userlib->get_user_info($user);
	}

	if (! empty($params['date_format'])) {
		$functionName = "get_" . $params['date_format'];
		if (method_exists($tikilib, $functionName)) {
			return $tikilib->$functionName($info['lastLogin']);
		} elseif ($params['date_format'] == 'timestamp') {
			return $info['lastLogin'];
		} else {
			return $tikilib->get_short_datetime($info['lastLogin']);
		}
	} else {
		return $tikilib->get_short_datetime($info['lastLogin']);
	}
}
