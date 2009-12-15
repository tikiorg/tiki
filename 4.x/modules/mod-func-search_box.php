<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_search_box_info() {
	return array(
		'name' => tra('Search Box'),
		'description' => tra('Advanced search (for wiki, articles, blogs etc).'),
		'prefs' => array('feature_search'),
	);
}

function module_search_box( $mod_reference, $module_params ) {
	
}
