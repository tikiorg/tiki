<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_usercount_info()
{
	return array(
		'name' => tra('User Count'),
		'documentation' => 'PluginUserCount',
		'description' => tra('Display number of users for a site or group'),
		'prefs' => array( 'wikiplugin_usercount' ),
		'body' => tra('Group name'),
		'icon' => 'img/icons/group_gear.png',
		'params' => array(
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('List of colon separated groups where a consolidated user count for multiple groups is needed.'),
				'separator' => ':',
				'default' => '',
			),		
		),
	);
}

function wikiplugin_usercount($data, $params)
{
	$userlib = TikiLib::lib('user');

	extract($params, EXTR_SKIP);

	if ( isset( $params['groups'] ) ) {
		$groups = $params['groups'];
		$numusers = $userlib->count_users_consolidated($groups);
	} else {
		$numusers = $userlib->count_users($data);
	}

	return $numusers;
}
