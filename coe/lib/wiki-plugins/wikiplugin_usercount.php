<?php

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
		'description' => tra('Displays the number of registered users'),
		'prefs' => array( 'wikiplugin_usercount' ),
		'body' => tra('Group name'),
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

?>
