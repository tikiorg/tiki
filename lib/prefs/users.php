<?php

function prefs_users_list() {
	return array(
		'users_serve_avatar_static' => array(
			'name' => tra('Serve avatar images statically'),
			'description' => tra('When enabled, feature checks and permission checks will be skipped.'),
			'type' => 'flag',
		),
		'users_prefs_display_timezone' => array(
			'type' => 'radio',
			'options' => array(
				'Site' => tra('Use site default to show times'),
				'' => tra('Detect user timezone (if browser allows). Otherwise use site default.'),
			),
		),
	);
}
