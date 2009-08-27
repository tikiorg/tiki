<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_directory_stats_info() {
	return array(
		'name' => tra('Directory statistics'),
		'description' => tra('Displays statistics about the directory, including the number of sites validated and to validate, the number of categories, of searches and of visited links.'),
		'prefs' => array( 'feature_directory' ),
		'params' => array()
	);
}

function module_directory_stats( $mod_reference, $module_params ) {
	global $prefs, $tikilib, $smarty;
	
	$ranking = $tikilib->dir_stats();
	$smarty->assign('modDirStats', $ranking);
}