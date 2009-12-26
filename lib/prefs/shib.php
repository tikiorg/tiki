<?php

function prefs_shib_list() {
	return array(
		'shib_group' => array(
			'name' => tra('Default group'),
			'type' => 'text',
			'size' => 40,
		),
		'shib_usegroup' => array(
			'name' => tra('Create with default group'),
			'type' => 'flag',
		),
		'shib_affiliation' => array(
			'name' => tra('Valid affiliations'),
			'type' => 'text',
			'size' => 40,
			'hint' => tra('Separate multiple affiliations with commas'),
		),
		'shib_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
		),
		'shib_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => 'flag',
		),
	);	
}
