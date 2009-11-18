<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_image_galleries_info() {
	return array(
		'name' => tra('Last modified image galleries'),
		'description' => tra('Displays the specified number of image galleries, starting from the most recently modified.'),
		'prefs' => array("feature_galleries"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_image_galleries( $mod_reference, $module_params ) {
	global $smarty, $user;
	global $imagegallib; include_once 'lib/imagegals/imagegallib.php';
	$ranking = $imagegallib->list_visible_galleries(0, $mod_reference["rows"], 'lastModif_desc', $user, '');
	
	$smarty->assign('modLastGalleries', $ranking["data"]);
}
