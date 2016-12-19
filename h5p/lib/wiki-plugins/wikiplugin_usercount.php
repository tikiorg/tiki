<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_usercount_info()
{
	return array(
		'name' => tra('User Count'),
		'documentation' => 'PluginUserCount',
		'description' => tra('Display number of users for a site or one or more groups'),
		'prefs' => array( 'wikiplugin_usercount' ),
		'body' => tra('Group name. If left blank, the total number of users for the site will be shown.'),
		'iconname' => 'group',
		'introduced' => 1,
		'params' => array(
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('List of colon separated groups where a consolidated user count for multiple
					groups is needed. Users in multiple groups are counted only once. If left blank then the behaviour
					is defined by the body parameter settings.'),
				'since' => '14.1',
				'separator' => ':',
				'filter' => 'groupname',
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
