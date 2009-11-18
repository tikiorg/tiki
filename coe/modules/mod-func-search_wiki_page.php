<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_search_wiki_page_info() {
	return array(
		'name' => tra('Search Wiki Page'),
		'description' => tra('Search for a wiki page by name.'),
		'prefs' => array('feature_search'),
	);
}

function module_search_wiki_page( $mod_reference, $module_params ) {
	
}
