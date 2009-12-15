<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_logo_info() {
	return array(
		'name' => tra('Logo'),
		'description' => tra('Site logo, title and subtitle.'),
		'prefs' => array('feature_sitelogo'),
	);
}

function module_logo( $mod_reference, $module_params ) {
	
}
