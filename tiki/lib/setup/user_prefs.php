<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/user_prefs.php,v 1.1 2007-10-06 15:18:45 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( $user ) {

	$user_preferences = array(); // Used for cache

	$group = $userlib->get_user_default_group($user);
	$user_dbl = $tikilib->get_user_preference($user, 'user_dbl', 'y');
	$allowMsgs = $tikilib->get_user_preference($user, 'allowMsgs', 'y');
	$tasks_maxRecords = $tikilib->get_user_preference($user, 'tasks_maxRecords');

	$smarty->assign('user', $user);
	$smarty->assign('default_group',$group);

	$smarty->assign('group', $group);
	$smarty->assign('user_dbl', $user_dbl);
	$smarty->assign('allowMsgs', $allowMsgs);
	$smarty->assign('tasks_maxRecords', $tasks_maxRecords);

} else {

	$allowMsgs = 'n';

}

if ( isset($_SERVER['REMOTE_ADDR']) ) {
	$IP = $_SERVER['REMOTE_ADDR'];
	$smarty->assign('IP', $IP);
}
