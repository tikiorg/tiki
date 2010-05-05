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

function module_random_images_info() {
	return array(
		'name' => tra('Random image'),
		'description' => tra('Displays a random image.'),
		'prefs' => array( 'feature_galleries' ),
		'params' => array(
			'galleryId' => array(
				'name' => tra('Gallery identifier'),
				'description' => tra('If set to an image gallery identifier, restricts the chosen images to those in the identified gallery.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.'),
				'filter' => 'int'
			),
			'showlink' => array(
				'name' => tra('Show link'),
				'description' => tra('If set to "n", the image thumbnail does not link to the image.') . " " . tra('Default: "y"'),
				'filter' => 'word'
			),
			'showname' => array(
				'name' => tra('Show name'),
				'description' => tra('If set to "y", the name of the image is displayed.') . " " . tra('Default: "n"'),
				'filter' => 'word'
			),
			'showdescription' => array(
				'name' => tra('Show description'),
				'description' => tra('If set to "y", the description of the image is displayed.') . " " . tra('Default: "n"'),
				'filter' => 'word'
			)
		)
	);
}

function module_random_images( $mod_reference, $module_params ) {
	global $smarty;
	global $imagegallib; include_once ("lib/imagegals/imagegallib.php");
	
	if (isset($module_params["galleryId"])) {
		$galleryId = $module_params["galleryId"];
	} else {
		$galleryId = -1;
	}
	
	$ranking = $imagegallib->get_random_image($galleryId);
	$smarty->assign('img', $ranking);
}
