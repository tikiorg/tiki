<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function iconset_orange()
{
	/* This and themes/fivealive-lite/options/orange/icons/information.png are
	 * just a demo of how to override an icon in a theme option
	 * probably should be removed before release? */

	return array(
		'name' => tr('Theme icons'),
		'description' => tr('Icons to be used for this theme'),
		'tag' => 'span', // the default html tag to sorround the icon
		'icons' => array(
			'information' => array(
				'id' => 'eye', // This is a sample icon, change it to have a customized font-aewesome icon or change/add parameters to have an image or a glyphicon. See http://dev.tiki.org/Icons for more details
			),
		)
	);
}