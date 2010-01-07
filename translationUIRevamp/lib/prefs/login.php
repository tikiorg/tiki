<?php

function prefs_login_list() {
	return array(
		'login_is_email' => array(
			'name' => tra('Use email as username'),
			'description' => tra('Instead of creating new usernames, use the user\'s email address for authentication.'),
			'type' => 'flag',
		),
	);
}

