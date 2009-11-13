<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_visitors_info() {
	return array(
		'name' => tra('Last Visitors'),
		'description' => tra('Displays information about the specified number of users in decreasing order of last login time.'),
		'params' => array(
			'showavatars' => array(
				'name' => tra('Show avatars'),
				'description' => tra('If set to "y", show user avatars.') . ' ' . tra('Default:') . ' "n"'
			),
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in user names allowed before truncating.'),
				'filter' => 'int'
			)
		),
		'common_params' => array('nonums', 'rows'),
	);
}

function module_last_visitors( $mod_reference, $module_params ) {
	global $smarty;
	global $userlib; include_once('lib/userslib.php');

	$last_visitors = $userlib->get_users(0, $mod_reference["rows"], 'currentLogin_desc');
	$smarty->assign('modLastVisitors', $last_visitors['data']);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
	$smarty->assign('showavatars', isset($module_params["showavatars"]) ? $module_params["showavatars"] : 'n');
}
