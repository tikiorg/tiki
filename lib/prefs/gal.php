<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function prefs_gal_list()
{

	if ((extension_loaded('gd') && function_exists('gd_info'))) {
		$gdinfo = gd_info();
		$gdlib = tr('GD %0 detected.', $gdinfo["GD Version"]);
	} else {
		$gdlib = tra('GD not detected.');
	}
	if ((extension_loaded('imagick') && function_exists('imagick_rotate'))) {
		$imagicklib = tr('Imagick %0 detected.', phpversion('imagick'));
	} else {
		$imagicklib = tra('Imagick 0 not detected.');
	}

	return [
		'gal_batch_dir' => [
			'name' => tra('Batch loading directory'),
			'type' => 'text',
			'default' => '',
			'hint' => tra('Needs to be a web-readable directory.')
		],
		'gal_image_mouseover' => [
			'name' => tra('Display image information in a mouseover box'),
			'description' => '',
			'type' => 'list',
			'options' => [
				'n' => tra('No'),
				'y' => tra('Yes'),
				'only' => tra('Yes, and don\'t display that information under the image'),
			],
			'default' => 'n',
		],
		'gal_list_name' => [
			'name' => tra('Name'),
			'description' => '',
			'type' => 'flag',
			'default' => 'y',
		],
		'gal_list_parent' => [
			'name' => tra('Parent'),
			'type' => 'flag',
			'default' => 'n',
		],
		'gal_list_description' => [
			'name' => tra('Description'),
			'type' => 'flag',
			'default' => 'y',
		],
		'gal_list_created' => [
			'name' => tra('Created'),
			'type' => 'flag',
			'default' => 'n',
		],
		'gal_list_lastmodif' => [
			'name' => tra('Last modified'),
			'type' => 'flag',
			'default' => 'y',
		],
		'gal_list_user' => [
			'name' => tra('User'),
			'type' => 'flag',
			'default' => 'n',
		],
		'gal_list_imgs' => [
			'name' => tra('Images'),
			'type' => 'flag',
			'default' => 'y',
		],
		'gal_list_visits' => [
			'name' => tra('Visits'),
			'type' => 'flag',
			'default' => 'y',
		],
		'gal_match_regex' => [
			'name' => tra('Uploaded image names must match regex'),
			'type' => 'text',
			'default' => '',
		],
		'gal_nmatch_regex' => [
			'name' => tra('Uploaded image names cannot match regex'),
			'type' => 'text',
			'default' => '',
		],
		'gal_use_db' => [
			'name' => tra('Storage'),
			'type' => 'radio',
			'options' => [
				'y' => tra('Store in database'),
				'n' => tra('Store in directory'),
			],
			'default' => 'y',
		],
		'gal_use_dir' => [
			'name' => tra('Directory path'),
			'type' => 'text',
			'default' => '',
			'hint' => tra('If you change this directory, move any images to the new directory either manually or using the \'Mover\' below.')
		],
		'gal_use_lib' => [
			'name' => tra('Image processing library'),
			'type' => 'list',
			'options' => [
				'gd' => tra('GD'),
				'imagick' => tra('Imagick'). ' 0',
			],
			'default' => 'imagick',
			'hint' => $gdlib . '  ' . $imagicklib
		],
	];
}
