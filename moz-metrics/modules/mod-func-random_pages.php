<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_random_pages_info() {
	return array(
		'name' => tra('Random Pages'),
		'description' => tra('Displays the specified number of random wiki pages.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(),
		'common_params' => array("rows", "nonums")
	);
}

function module_random_pages( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$pages=$tikilib->list_pages(0, $mod_reference["rows"], "random", '', '', true, true);
	
	$smarty->assign('modRandomPages', $pages["data"]);
}
