<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_usergroup_tracker_info() {
	return array(
		'name' => tra('User-Group Tracker'),
		'description' => tra('User and Group tracker links.'),
		'prefs' => array('feature_trackers'),
	);
}

function module_usergroup_tracker( $mod_reference, $module_params ) {
	
}
