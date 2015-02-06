<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_last_images_info()
{
	return array(
		'name' => tra('Newest Images'),
		'description' => tra('List the specified number of images, starting from the most recently added.'),
		'prefs' => array("feature_galleries"),
		'params' => array(
			'galleryId' => array(
				'name' => tra('Gallery identifier'),
				'description' => tra('If set to an image gallery identifier, restricts the images to those in the identified gallery.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.'),
				'filter' => 'int',
				'profile_reference' => 'file_gallery',
			),
			'content' => array(
				'name' => tra('Link content'),
				'description' => tra('Display the links as image names or thumbnails.') . " " . tra('Possible values: "names" or "thumbnails". Default value: "names"'),
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_images($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$imagegallib = TikiLib::lib('imagegal');
	
	$smarty->assign("content", isset($module_params["content"]) ? $module_params["content"] : "names");
	$galleryId = isset($module_params["galleryId"]) ? $module_params["galleryId"] : -1;
	
	$ranking = $imagegallib->list_images(0, $mod_reference["rows"], 'created_desc', '', $galleryId);
	$smarty->assign('modLastImages', $ranking["data"]);
}
