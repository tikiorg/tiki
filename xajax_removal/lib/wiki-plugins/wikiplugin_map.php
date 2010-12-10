<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Removed in Tiki 7
// TODO replace this with an alias perhaps?

function wikiplugin_map_info() {
	return array(
		'name' => tra('Map'),
		'documentation' => tra('PluginMap'),	
		'description' => tra('Displays a map (removed in Tiki 7'),
		'prefs' => array( 'wikiplugin_map' ),
		'params' => array(
			'mapfile' => array(
				'required' => true,
				'name' => tra('Map File'),
				'description' => tra('Map file identifier'),
			),
			'extents' => array(
				'required' => false,
				'name' => tra('Extents'),
				'description' => tra('Extents'),
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the map'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the map'),
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the map'),
			),
		),
	);
}

function wikiplugin_map($data, $params) {
	return tra('Map feature removed in Tiki 7');
}
