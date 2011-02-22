<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_live_support_info() {
	return array(
		'name' => tra('Live Support'),
		'description' => tra('Tells users whether a live support operator is present and allows them to request support if possible. If the user is an operator, links to the operator console.'),
		'prefs' => array( 'feature_live_support' ),
		'params' => array()
	);
}

function module_live_support( $mod_reference, $module_params ) {
	global $access;
	global $user;
	global $smarty;
	global $lslib; include_once ('lib/live_support/lslib.php');
	global $lsadminlib ; include_once ('lib/live_support/lsadminlib.php');
	
	$smarty->assign('modsupport', $lslib->operators_online());
	if ($lsadminlib->is_operator($user)) {
				$smarty->assign('user_is_operator','y');
	} else {
				$smarty->assign('user_is_operator','n');
	}
}
