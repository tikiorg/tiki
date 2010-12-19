<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-login_box.php 26808 2010-04-28 12:30:41Z jonnybradley $

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
	
}
