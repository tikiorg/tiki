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

function module_share_info() {
	return array(
		'name' => tra('Share'),
		'description' => tra('Links for sharing, reporting etc.'),
		'params' => array(
			'report' => array(
				'name' => tra('Report'),
				'description' => tra('Report to Webmaster') . ' (y/n)',
				'filter' => 'alpha',
			),
			'share' => array(
				'name' => tra('Share'),
				'description' => tra('Share this page') . ' (y/n)',
				'filter' => 'alpha',
			),
			'email' => array(
				'name' => tra('Email'),
				'description' => tra('Email this page') . ' (y/n)',
				'filter' => 'alpha',
			),
		),
	);
}

function module_share( $mod_reference, $module_params ) {
}
