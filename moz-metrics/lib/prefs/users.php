<?php

function prefs_users_list() {
	return array(
		'users_serve_avatar_static' => array(
			'name' => tra('Serve avatar images statically'),
			'description' => tra('When enabled, feature checks and permission checks will be skipped.'),
			'type' => 'flag',
		),
	);
}
