<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_quizzes_info() {
	return array(
		'name' => tra('Top Quizzes'),
		'description' => tra('Displays the specified number of quizzes with links to them, starting with the one having the most hits.'),
		'prefs' => array( 'feature_quizzes' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_quizzes( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_quiz_sum_stats(0, $mod_reference["rows"], 'timesTaken_desc', '');
	$smarty->assign('modTopQuizzes', $ranking["data"]);
}
