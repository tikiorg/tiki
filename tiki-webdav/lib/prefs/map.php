<?php

function prefs_map_list() {
	return array(
		'map_path' => array(
			'name' => tra('full path to mapfiles'),
			'type' => 'text',
			'size' => '50',
		),
		'map_help' => array(
			'name' => tra('Wiki Page for Help'),
			'type' => 'text',
			'size' => '50',
		),
		'map_comments' => array(
			'name' => tra('Wiki Page for Comments'),
			'type' => 'text',
			'size' => '25',
		),
	);
}
