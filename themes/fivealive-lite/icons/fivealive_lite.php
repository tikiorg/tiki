<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function iconset_fivealive_lite()
{
	/* This and themes/fivealive-lite/icons/information.png are
	 * just a demo of how to override an icon in a theme
	 * probably should be removed before release? */

	return array(
		'name' => tr('Theme icons'),
		'description' => tr('Icons to be used for this theme'),
		//path to the source icon set that is to be overridden by 'icons' subarray specified below if not the default one
		//'source' => 'themes/base_files/iconsets/default.php',
		'tag' => 'span', // the default html tag to surround the icon
		'icons' => array(
			'information' => array(
				'id' => 'info-circle', // This is a sample icon, change it to have a theme specific icon
			),
		)
	);
}