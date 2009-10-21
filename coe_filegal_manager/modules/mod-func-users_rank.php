<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_users_rank_info() {
	return array(
		'name' => tra('Top users'),
		'description' => tra('Displays the specified number of users and their score, starting from the one with the highest score.'),
		'prefs' => array( 'feature_score' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_users_rank( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	$users_rank = $tikilib->rank_users($mod_reference["rows"]);
	$smarty->assign('users_rank', $users_rank);
}
