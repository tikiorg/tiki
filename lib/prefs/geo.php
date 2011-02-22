<?php

function prefs_geo_list()
{
	return array(
		'geo_locate_wiki' => array(
			'name' => tra('Geolocate wiki pages'),
			'description' => tra('Provide controls to geolocate wiki pages from the edit functionality.'),
			'type' => 'flag',
		),
		'geo_locate_article' => array(
			'name' => tra('Geolocate articles'),
			'description' => tra('Provide controls to geolocate articles from the edit functionality.'),
			'type' => 'flag',
		),
		'geo_locate_blogpost' => array(
			'name' => tra('Geolocate blog posts'),
			'description' => tra('Provide controls to geolocate blog posts from the edit functionality.'),
			'type' => 'flag',
		),
	);
}

