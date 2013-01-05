<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		),
	);
}

function wikiplugin_usercount($data, $params)
{
	global $tikilib, $userlib;

	extract($params, EXTR_SKIP);

	$numusers = $userlib->count_users($data);

	return $numusers;
}
