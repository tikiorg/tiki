<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_file_galleries_info() {
	return array(
		'name' => tra('Last modified file galleries'),
		'description' => tra('Displays the specified number of file galleries, starting from the most recently modified.'),
		'prefs' => array("feature_file_galleries"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_file_galleries( $mod_reference, $module_params ) {
	global $tikilib, $smarty, $prefs;
	$ranking = $tikilib->get_files(0, $mod_reference["rows"], 'lastModif_desc', null, $prefs['fgal_root_id'], false, true, false, false);
	
	$smarty->assign('modLastFileGalleries', $ranking["data"]);
}
