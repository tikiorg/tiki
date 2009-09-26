<?php

function prefs_session_list() {
	return array (
		'session_db' => array(
			'name' => tra('Store session in database'),
			'description' => tra('Store session in database'),
			'type' => 'flag',
		),
		'session_lifetime' => array(
			'name' => tra('Session lifetime'),
			'description' => tra('Session lifetime'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '4',
		),
	);
}
