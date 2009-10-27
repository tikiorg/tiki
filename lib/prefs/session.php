<?php

function prefs_session_list() {
	return array (
		'session_storage' => array(
			'name' => tra('Session storage location'),
			'description' => tra('Select where the session information should be stored. Memcache sessions require memcache to be configured.'),
			'type' => 'list',
			'options' => array(
				'default' => tra('Default (from php.ini)'),
				'db' => tra('Database'),
				'memcache' => tra('Memcache'),
			),
		),
		'session_lifetime' => array(
			'name' => tra('Session lifetime'),
			'description' => tra('Session lifetime'),
			'hint' => tra('Value provided in minutes'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '4',
		),
	);
}
