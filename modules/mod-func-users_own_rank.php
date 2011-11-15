<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_users_own_rank_info() {
	return array(
		'name' => tra('My score'),
		'description' => tra('Displays the logged user\'s rank and score.'),
		'prefs' => array( 'feature_score' ),
		'params' => array()
	);
}

function module_users_own_rank( $mod_reference, $module_params ) {
	global $scorelib, $smarty, $user;
	include_once('lib/score/scorelib.php');

	$position = $scorelib->user_position($user);
	$smarty->assign('position', $position);
	$score = $scorelib->get_user_score($user);
	$smarty->assign('score', $score);
	$count = $scorelib->count_users(0);
	$smarty->assign('count', $count);
	$smarty->assign('user', $user);
}
