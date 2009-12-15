<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_rsslist_info() {
	return array(
		'name' => tra('Feeds List'),
		'description' => tra('List of feeds available on this site.'),
		'prefs' => array(),
	);
}

function module_rsslist( $mod_reference, $module_params ) {
	
}
