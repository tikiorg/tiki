<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_geo_list()
{
	return array(
		'geo_locate_wiki' => array(
			'name' => tra('Geolocate wiki pages'),
			'description' => tra('Provide controls to indicate a geographic location of wiki pages in the edit form.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'geo_locate_article' => array(
			'name' => tra('Geolocate articles'),
			'description' => tra('Provide controls to indicate a geographic location in the article edit form.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'geo_locate_blogpost' => array(
			'name' => tra('Geolocate blog posts'),
			'description' => tra('Provide controls to indicate a geographic location in the blog post edit form.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'geo_tilesets' => array(
			'name' => tra('Available tile layers on maps'),
			'description' => tra('Enables replacement of the default OpenStreetMap tiles with tiles from other mapping services, such as Google or Bing.'),
			'hint' => tr(
				'Valid options are: %0',
				implode(
					', ',
					array(
						'openstreetmap',
						'mapquest_street',
						'mapquest_aerial',
						'google_street',
						'google_satellite',
						'google_physical',
						'google_hybrid',
						'blank',
						/* Needs additional testing
						'visualearth_road',
						'visualearth_aerial',
						'visualearth_hybrid',
						'yahoo_street',
						'yahoo_satellite',
						'yahoo_hybrid',
						*/
					)
				)
			),
			'type' => 'text',
			'filter' => 'word',
			'separator' => ',',
			'default' => array('openstreetmap'),
			'tags' => array('advanced'),
		),
		'geo_google_streetview' => array(
			'name' => tr('Google Street View'),
			'description' => tr('Open Google Street View in a new window to see the visible coordinates.'),
			'dependencies' => array('gmap_key'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic', 'experimental'),
		),
		'geo_google_streetview_overlay' => array(
			'name' => tr('Google Street View Overlay'),
			'description' => tr('Open Google Street View in a new window to see the visible coordinates.'),
			'dependencies' => array('geo_google_streetview'),
			'warning' => tr('This is not guaranteed to work.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic', 'experimental'),
		),
		'geo_always_load_openlayers' => array(
			'name' => tr('Always load OpenLayers'),
			'description' => tr('Load the OpenLayers library even if no map is explicitly included in the page'),
			'type' => 'flag',
			'default' => 'n',
		),
		'geo_zoomlevel_to_found_location' => array(
			'name' => tr('Zoom to the found location'),
			'description' => tr('Zoom to street level when a searched-for location is found'),
			'type' => 'list',
			'options' => array(
					'street' => tra('Street level'),
					'town' => tra('Town level'),
					'region' => tra('Region level'),
					'country' => tra('Country level'),
					'continent' => tra('Continent level'),
					'world' => tra('World'),
				),
			'default' => 'street',
		),
		'geo_openlayers_version' => array(
			'name' => tr('OpenLayers Version'),
			'description' => tr(''),
			'type' => 'list',
			'options' => array(
					'ol2' => tra('OpenLayers 2.x (for use up to at least 15.x)'),
					'ol3' => tra('OpenLayers 3.x (experimental)'),
				),
			'default' => 'ol2',
		),
	);
}

