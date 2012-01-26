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

function module_babelfish_links_info() {
	return array(
		'name' => tra('Babel Fish Links'),
		'description' => tra('Offers a link to the Yahoo! Babel Fish translation service'),
		'prefs' => array( 'feature_babelfish' ),		
		'params' => array(),
	);
}

function module_babelfish_links( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	require_once('lib/Babelfish.php');
	$smarty->assign('babelfish_logo', Babelfish::logo($prefs['language']));
}
