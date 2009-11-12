<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_login_box_info() {
	return array(
		'name' => tra('Login'),
		'description' => tra('Login box'),
		'prefs' => array(),
		'params' => array(
			'input_size' => array(
				'name' => tra('Input size'),
				'description' => tra('Number of characters for username and password input fields.'),
				'filter' => 'int'
			),
		)
	);
}

function module_login_box( $mod_reference, $module_params ) {
	
}
