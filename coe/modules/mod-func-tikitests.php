<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_tikitests_info() {
	return array(
		'name' => tra('Tiki Tests'),
		'description' => tra('Tiki test suite helper.'),
		'prefs' => array('feature_tikitests'),
	);
}

function module_tikitests( $mod_reference, $module_params ) {
	
}
