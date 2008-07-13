<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Handle the current user prefs in session
if ( ! isset($_SESSION['u_info']['login']) || $_SESSION['u_info']['login'] != $user || $_SESSION['need_reload_prefs'] ) {
	$_SESSION['need_reload_prefs'] = false;
	$_SESSION['u_info'] = array();
	$_SESSION['u_info']['prefs'] = array();
	$_SESSION['u_info']['login'] = $user;
	$_SESSION['u_info']['group'] = ( $user ) ? $userlib->get_user_default_group($user) : '';
}

// Define the globals $u_info array for use in php / smarty
$u_info =& $_SESSION['u_info'];
$smarty->assign_by_ref('u_info', $u_info);

if ( $user ) {

	// Initialize user prefs
	$user_preferences = array(); // Used for cache
	$user_preferences[$user] =& $_SESSION['u_info']['prefs'];

	$group =& $_SESSION['u_info']['group'];
	$smarty->assign_by_ref('group', $group);
	$smarty->assign_by_ref('user', $user);
	$smarty->assign_by_ref('default_group', $group);

	// Get all user prefs in one query
	$tikilib->get_user_preferences($user);

	// Prefs overriding
	$prefs = array_merge($prefs, $user_preferences[$user]);

	// Copy some user prefs that doesn't have the same name as the related site pref
	//   in order to symplify the overriding and the use 
	if ( isset($prefs['theme']) && $prefs['theme'] != '' ) {
		$prefs['style'] = $prefs['theme'];
	}

} else {
	$allowMsgs = 'n';
}

if ( isset($_SERVER['REMOTE_ADDR']) ) {
	$IP = $_SERVER['REMOTE_ADDR'];
	$smarty->assign('IP', $IP);
}

if ($prefs['users_prefs_display_timezone'] == 'Site' || (isset($user_preferences[$user]['display_timezone']) && $user_preferences[$user]['display_timezone'] == 'Site')) {
	// Everybody stays in the time zone of the server
	$prefs['display_timezone'] = $prefs['server_timezone'];
} elseif ( ! isset($user_preferences[$user]['display_timezone']) || $user_preferences[$user]['display_timezone'] == '' ) {
	// If the display timezone is not known ...
	if ( isset($_COOKIE['local_tz']) && eregi('[A-Z]', $_COOKIE['local_tz']) ) {
		//   ... we try to use the timezone detected by javascript and stored in cookies
		if ( $_COOKIE['local_tz'] == 'CEST' || $_COOKIE['local_tz'] == 'HAEC' ) {
			// CEST (and HAEC, returned by Safari on Mac) is not recognized as a DST timezone (with daylightsavings) by PEAR Date
			//  ... So use one equivalent timezone name
			$prefs['display_timezone'] = 'Europe/Paris';
		} else {
			$prefs['display_timezone'] = $_COOKIE['local_tz'];
		}
		if ( ! Date_TimeZone::isValidID($prefs['display_timezone']) ) {
			$prefs['display_timezone'] = $prefs['server_timezone'];
		}
	} else {
		// ... and we fallback to the server timezone if the cookie value is not available
		$prefs['display_timezone'] = $prefs['server_timezone'];
	}
}
