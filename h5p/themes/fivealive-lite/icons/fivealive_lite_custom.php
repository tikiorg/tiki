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

/* The correct naming of the function is important. Read more here: themes.tiki.org.
* @return array
*/

function iconset_fivealive_lite_custom()
{
	return array(
		'name' => tr('Custom icons'),
		'description' => tr('Custom icons for the theme'),
		'tag' => 'span', // the default html tag to surround the icon
		'icons' => array(
			'information' => array(
				'id' => 'info-circle', // This is a sample icon, change it to have a theme specific icon
			),
		)
	);
}
