<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_active_blogs_info() {
	return array(
		'name' => tra('Most Active blogs'),
		'description' => tra('Displays the specified number of blogs with links to them, from the most active one to the least.') . tra('Blog activity measurement can be more or less accurate.'),
		'prefs' => array( 'feature_blogs' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_active_blogs( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_blogs(0, $mod_reference["rows"], 'activity_desc', '');
	
	$smarty->assign('modTopActiveBlogs', $ranking["data"]);
}
