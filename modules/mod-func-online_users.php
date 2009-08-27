<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_online_users_info() {
	return array(
		'name' => tra('Who is online'),
		'description' => tra('Displays a list of logged-in users, using their real names if possible. Depending on settings, links to send messages and see user information may be shown.'),
		'prefs' => array(),
		'params' => array(),
	);
}

function module_online_users( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$online_users = $tikilib->get_online_users();
	
	$smarty->assign('online_users', $online_users);
}