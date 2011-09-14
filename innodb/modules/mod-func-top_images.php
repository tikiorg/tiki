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

function module_top_images_info() {
	return array(
		'name' => tra('Top Images'),
		'description' => tra('Displays the specified number of images with links to them, from the most visited one to the least.'),
		'prefs' => array( 'feature_galleries' ),
		'params' => array(
			'content' => array(
				'name' => tra('Link content'),
				'description' => tra('Display the links as image names or thumbnails.') . " " . tra('Possible values: "names" or "thumbnails". Default value: "names"')
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_images( $mod_reference, $module_params ) {
	global $smarty;
	global $imagegallib; include_once ("lib/imagegals/imagegallib.php");
	
	$smarty->assign("content", isset($module_params["content"]) ? $module_params["content"] : "names");

	$ranking = $imagegallib->list_images(0, $mod_reference["rows"], 'hits_desc', '');
	$smarty->assign('modTopImages', $ranking["data"]);
}
