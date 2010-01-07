<?php

function prefs_min_list() {
	return array(
		'min_username_length' => array(
			'name' => tra('Minimum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'min_pass_length' => array(
			'name' => tra('Minimum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);
}
	
