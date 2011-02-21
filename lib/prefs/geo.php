<?php

function prefs_geo_list()
{
	return array(
		'geo_locate_wiki' => array(
			'name' => tra('Geolocate wiki pages'),
			'description' => tra('Provide controls to geolocate wiki pages from the edit functionality.'),
			'type' => 'flag',
		),
	);
}

