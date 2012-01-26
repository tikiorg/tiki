<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_messages_unread_messages_info() {
	return array(
		'name' => tra('Unread inter-user messages'),
		'description' => tra('Displays to users their number of new inter-user messages and a link to their message box.'),
		'prefs' => array( 'feature_messages' ),
		'params' => array(
			'showempty' => array(
				'name' => tra('Show If Empty'),
				'description' => tra('Show the module when there are no messages waiting. y|n ') . tra('Default=y'),
				'required' => false,
			)
		)
	);
}

function module_messages_unread_messages( $mod_reference, $module_params ) {
	global $user, $tikilib, $smarty;
	$globalperms = Perms::get();

	if ($user && $globalperms->messages) {
		$modUnread = $tikilib->user_unread_messages($user);
		if ($modUnread > 0 || !isset($module_params['showempty']) || $module_params['showempty'] == 'y') {
			$smarty->assign('modUnread', $modUnread);
			$smarty->assign('tpl_module_title', tra("Messages"));
		}
	}
}
