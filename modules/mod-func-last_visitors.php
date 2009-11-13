<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_visitors_info() {
	return array(
		'name' => tra('Last Visitors'),
		'description' => tra('Most recent visitors.'),
		'prefs' => array(),
		'common_params' => array(
			'nonums',
		),
	);
}

function module_last_visitors( $mod_reference, $module_params ) {
	
}
