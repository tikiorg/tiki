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

// Made by Yoni with Toggg great help
// Parameters to set for the module
// client=pub-xxxxxxxxxxxxxxxx as it appears on the Google code
// display= global banner format as it appear in the Google code, ex: display=468*60_as
// color_border=
// color_bg=
// color_link=
// color_url=
// color_text=
// colors as you set it for you own purpose ex: color_border=edeed5
// If you don't set them the colors will be Google defaults
// Usage example :
// {MODULE(module=>adsense,client=pub-xxxxxxxxxxxxxxxx,display=468*60_as,color_border=edeed5,color_bg=edeed5,color_link=0000CC,color_url=008000,color_text=000000)}{MODULE}

function module_adsense_info() {
	return array(
		'name' => tra('Google AdSense'),
		'description' => tra('Displays a text/image Google AdSense advertisement. This module should be updated to support new Google code.'),
		'prefs' => array( ),
		'params' => array(
			'ad_channel' => array(
				'name' => 'ad_channel',
				'description' => tra('Advertisement channel, as optionally provided by Google.'),
				'filter' => 'striptags',
			),
			'client' => array(
				'name' => 'client',
				'description' => tra('As provided by Google. Format: "pub-xxxxxxxxxxxxxxxx"'),
				'required' => true,
			),
			'display' => array(
				'name' => 'display',
				'description' => tra('Global banner format as provided by Google. For example: "display=468*60_as"'),
				'filter' => 'striptags',
				'required' => true,
			),
			'color_bg' => array(
				'name' => 'color_bg',
				'description' => tra('Background color, as optionally provided by Google.'),
				'filter' => 'striptags',
			),
			'color_border' => array(
				'name' => 'color_border',
				'description' => tra('Border color, as optionally provided by Google.'),
				'filter' => 'striptags',
			),
			'color_link' => array(
				'name' => 'color_link',
				'description' => tra('Link color, as optionally provided by Google.'),
				'filter' => 'striptags',
			),
			'color_text' => array(
				'name' => 'color_text',
				'description' => tra('Text color, as optionally provided by Google.'),
				'filter' => 'striptags',
			),
			'color_url' => array(
				'name' => 'color_url',
				'description' => tra('URL color, as optionally provided by Google.'),
				'filter' => 'striptags',
			)
		),
	);
}

function module_adsense( $mod_reference, $module_params ) {
	global $smarty;

	$smarty->assign(array('ad_channel' => $module_params['ad_channel'],
		'client' => $module_params['client'],
		'display' => $module_params['display'],
		'color_bg' => $module_params['color_bg'],
		'color_border' => $module_params['color_border'],
		'color_link' => $module_params['color_link'],
		'color_text' => $module_params['color_text'],
		'color_url' => $module_params['color_url']));
}
