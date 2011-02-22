<?php

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}


function module_locator_info() {
	return array(
		'name' => tra('Locator'),
		'description' => tra('Presents a map with the geolocated content within the page.'),
		'prefs' => array(),
		'params' => array(
		),
	);
}

function module_locator($mod_reference, $module_params) {
	$headerlib = TikiLib::lib('header');

	$headerlib->add_map();
}

