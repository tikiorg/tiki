<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

