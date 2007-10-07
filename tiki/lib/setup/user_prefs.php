<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/user_prefs.php,v 1.2 2007-10-07 09:32:36 nyloth Exp $
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
if ( ! isset($_SESSION['user']['login']) || $_SESSION['user']['login'] != $user ) {
	$_SESSION['user'] = array();
	$_SESSION['user']['prefs'] = array();
	$_SESSION['user']['login'] = $user;
	$_SESSION['user']['group'] = ( $user ) ? $userlib->get_user_default_group($user) : '';
}

// Define the globals $u_info and $u_prefs array for use in php / smarty
$u_info =& $_SESSION['user'];
$u_prefs =& $_SESSION['user']['prefs'];
$smarty->assign_by_ref('u_info', $u_info);
$smarty->assign_by_ref('u_prefs', $u_prefs);

if ( $user ) {

	$user_preferences = array(); // Used for cache
	$user_preferences[$user] =& $_SESSION['user']['prefs'];
	$group =& $_SESSION['user']['group'];
	$smarty->assign_by_ref('group', $group);
	$smarty->assign_by_ref('user', $user);
	$smarty->assign_by_ref('default_group', $group);

	// Get some user prefs in one query
	$needed_user_prefs = array(
		'user_dbl' => 'y',
		'allowMsgs' => 'y',
		'tasks_maxRecords' => null,
	);
	$tikilib->get_user_preferences($user, $needed_user_prefs, true);

	// One global var per user prefs that are known at this stage
	//   (deprecated -> consider using $u_prefs instead for the current user)
	extract($user_preferences[$user]);

	// Smarty assignations
	//   (deprecated -> need to be replaced by the use of smarty $u_info array);
	foreach ( $needed_user_prefs as $k => $v ) $smarty->assign($k, $$k);

} else {
	$allowMsgs = 'n';
}

if ( isset($_SERVER['REMOTE_ADDR']) ) {
	$IP = $_SERVER['REMOTE_ADDR'];
	$smarty->assign('IP', $IP);
}
