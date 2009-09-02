<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_files_info() {
	return array(
		'name' => tra('Top files'),
		'description' => tra('Displays the specified number of files with links to them, starting with the one with most hits.'),
		'prefs' => array( 'feature_file_galleries' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_files( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_files(0, $mod_reference["rows"], 'hits_desc', '');
	
	$smarty->assign('modTopFiles', $ranking["data"]);
}
