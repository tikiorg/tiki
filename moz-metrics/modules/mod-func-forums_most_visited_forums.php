<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_forums_most_visited_forums_info() {
	return array(
		'name' => tra('Most visited forums'),
		'description' => tra('Displays the specified number of the forums with the most visits.'),
		'prefs' => array( 'feature_forums' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_forums_most_visited_forums( $mod_reference, $module_params ) {
	global $smarty;
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	
	$ranking = $ranklib->forums_ranking_most_visited_forums($mod_reference["rows"]);
	$smarty->assign('modForumsMostVisitedForums', $ranking["data"]);
}