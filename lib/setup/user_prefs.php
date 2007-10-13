<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/user_prefs.php,v 1.6 2007-10-13 16:53:12 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

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

	// Keep some useful sites values available before overriding with user prefs
	$user_overrider_prefs = array('language', 'style', 'userbreadCrumb');
	foreach ( $user_overrider_prefs as $uop ) {
		$prefs['site_'.$uop] = $prefs[$uop];
	}

	// Initialize user prefs
	$user_preferences = array(); // Used for cache
	$user_preferences[$user] =& $_SESSION['u_info']['prefs'];

	$group =& $_SESSION['user']['group'];
	$smarty->assign_by_ref('group', $group);
	$smarty->assign_by_ref('user', $user);
	$smarty->assign_by_ref('default_group', $group);

	// Get all user prefs in one query
	$tikilib->get_user_preferences($user);

	// Prefs overriding
	$prefs = &array_merge($prefs, $user_preferences[$user]);

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
