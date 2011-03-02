<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_quickadmin_info() {
	return array(
		'name' => tra('Quick Admin'),
		'description' => tra('Some helpful tools for admins.'),
		'prefs' => array(),
		'params' => array(
			'mode' => array(
				'name' => tra('Mode'),
				'description' => tra('Display mode: module or header. Leave empty for module mode'),
			),
		)
	);
}

function module_quickadmin( $mod_reference, $module_params ) {
	global $prefs, $themegenlib;
	// include and setup themegen editor
	if ($prefs['feature_themegenerator'] === 'y' &&
			(strpos($_SERVER['SCRIPT_NAME'], 'tiki-admin.php') === false || strpos($_SERVER['QUERY_STRING'], 'page=look') === false)) {
		include_once 'lib/themegenlib.php';
		$themegenlib->setupEditor();
	}

}
