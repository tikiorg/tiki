<?php

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
		'params' => array()
	);
}

function module_messages_unread_messages( $mod_reference, $module_params ) {
	global $user, $tikilib, $smarty;
	$globalperms = Perms::get();

	if ($user && $globalperms->messages) {
		$modUnread = $tikilib->user_unread_messages($user);
	
		$smarty->assign('modUnread', $modUnread);
		$smarty->assign('tpl_module_title', tra("Messages"));
	}
}
