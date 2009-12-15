<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_directory_top_sites_info() {
	return array(
		'name' => tra('Top directory sites'),
		'description' => tra('Displays the specified number of the directory sites from most visited to least visited.'),
		'prefs' => array( 'feature_directory' ),
		'params' => array(),
		'common_params' => array('nonums')
	);
}

function module_directory_top_sites( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	$ranking = $tikilib->dir_list_all_valid_sites2(0, $mod_reference["rows"], 'hits_desc', '');

	$smarty->assign('modTopdirSites', $ranking["data"]);
}
