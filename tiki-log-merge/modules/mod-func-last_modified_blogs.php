<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_modified_blogs_info() {
	return array(
		'name' => tra('Last Modified blogs'),
		'description' => tra('Displays the specified number of blogs, starting from the most recently modified.'),
		'prefs' => array("feature_blogs"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_modified_blogs( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_blogs(0, $mod_reference["rows"], 'lastModif_desc', '');
	
	$smarty->assign('modLastModifiedBlogs', $ranking["data"]);
}
