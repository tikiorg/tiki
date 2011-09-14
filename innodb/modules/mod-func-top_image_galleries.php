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

function module_top_image_galleries_info() {
	return array(
		'name' => tra('Top image galleries'),
		'description' => tra('Displays the specified number of image galleries with links to them, starting with the one with most hits.'),
		'prefs' => array( 'feature_galleries' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_image_galleries( $mod_reference, $module_params ) {
	global $smarty;
	global $imagegallib; include_once ('lib/imagegals/imagegallib.php');
	$ranking = $imagegallib->list_visible_galleries(0, $mod_reference["rows"], 'hits_desc', 'admin', '');
	
	$smarty->assign('modTopGalleries', $ranking["data"]);
}
