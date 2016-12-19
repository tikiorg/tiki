<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_gmap_list()
{
	return array(
		'gmap_key' => array(
			'name' => tra('Google Maps API Key'),
			'description' => tra('Needed for Street View or other advanced features'),
			'type' => 'text',
			'size' => 87,
			'help' => 'http://code.google.com/apis/maps/signup.html',
			'filter' => 'striptags',
			'default' => '',
		),
		'gmap_defaultx' => array(
			'name' => tra('Default x for map center'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'filter' => 'striptags',
			'default' => '0',
		),
		'gmap_defaulty' => array(
			'name' => tra('Default y for map center'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'filter' => 'striptags',
			'default' => '0',
		),
		'gmap_defaultz' => array(
			'name' => tra('Default zoom level'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
				1 => tra('whole earth'),
				2 => 2,
				3 => 3,
				4 => 4,
				5 => tra('country size'),
				6 => 6,
				7 => 7,
				8 => 8,
				9 => 9,
				10 => 10,
				11 => tra('city size'),
				12 => 12,
				13 => 13,
				14 => 14,
				15 => 15,
				16 => 16, 
				17 => 17,
				18 => tra('max zoom'),
			),
			'default' => '1',
		),
		'gmap_article_list' => array(
			'name' => tra('Show map mode buttons in articles list'),
            'description' => tra(''),
			'type' => 'flag',
			'dependencies' => array(
				'geo_locate_article',
			),
			'default' => 'n',
		),
		'gmap_page_list' => array(
			'name' => tra('Show map mode buttons in page list'),
            'description' => tra(''),
			'type' => 'flag',
			'dependencies' => array(
				'geo_locate_wiki',
			),
			'default' => 'n',
		),
	);	
}
