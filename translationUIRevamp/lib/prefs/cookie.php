<?php

function prefs_cookie_list() {
	return array(
		'cookie_name' => array(
			'name' => tra('Cookie name'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
		),
		'cookie_domain' => array(
			'name' => tra('Domain'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
		),
		'cookie_path' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
		),
	);
}
	
