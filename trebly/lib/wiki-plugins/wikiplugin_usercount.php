<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays the number of total users or the number of users in a group
// Use:
// {USERCOUNT()}groupname{USERCOUNT}
//
// If no groupname is given returns all users

function wikiplugin_usercount_help() {
        return tra("Displays the number of registered users").":<br />~np~{USERCOUNT()}groupname{USERCOUNT}~/np~";
}

function wikiplugin_usercount_info() {
	return array(
		'name' => tra('User Count'),
		'documentation' => 'PluginUserCount',
		'description' => tra('Display number of users for a site or group'),
		'prefs' => array( 'wikiplugin_usercount' ),
		'body' => tra('Group name'),
		'icon' => 'pics/icons/group_gear.png',
		'params' => array(
		),
	);
}

function wikiplugin_usercount($data, $params) {
        global $tikilib;

        global $userlib;

        extract ($params,EXTR_SKIP);

        $numusers = $userlib->count_users($data);

        return $numusers;
}
