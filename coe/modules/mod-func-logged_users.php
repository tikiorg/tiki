<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_logged_users_info() {
	return array(
		'name' => tra('Online users'),
		'description' => tra('Displays the number of users logged in.'),
		'prefs' => array(),
		'params' => array(),
	);
}

function module_logged_users( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$logged_users = $tikilib->count_sessions();
	
	$smarty->assign('logged_users', $logged_users);
}