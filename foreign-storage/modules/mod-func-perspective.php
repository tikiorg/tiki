<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_perspective_info() {
	return array(
		'name' => tra('Perspective'),
		'description' => tra('Enables to change current perspective.'),
		'prefs' => array( 'feature_perspective' ),
		'params' => array()
	);
}

function module_perspective( $mod_reference, $module_params ) {
	global $perspectivelib; require_once 'lib/perspectivelib.php';
	global $smarty, $prefs;
	
	$perspectives = $perspectivelib->list_perspectives();
	$smarty->assign( 'perspectives', $perspectives );

	$smarty->assign( 'current_perspective', $perspectivelib->get_current_perspective( $prefs ) );
}

