<?php

// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_forum_posters_info() {
	return array(
		'name' => tra('Top Forum Posters'),
		'description' => tra('Displays the specified number of users who posted to forums, starting with the one having most posts.'),
		'prefs' => array( 'feature_forums' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_forum_posters( $mod_reference, $module_params ) {
	global $smarty;
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	$posters = $ranklib->forums_top_posters($mod_reference["rows"]);
	
	$smarty->assign('modTopForumPosters', $posters["data"]);
}
