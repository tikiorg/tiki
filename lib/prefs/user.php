<?php

function prefs_user_list() {
	return array(
		'user_show_realnames' => array(
			'name' => tra('Show user\'s real name instead of login (when possible)'),
			'description' => tra('Show user\'s real name instead of login (when possible)'),
			'help' => 'User+Preferences',
			'type' => 'flag',
		),
		'user_tracker_infos' => array(
			'name' => tra('Display UserTracker information on the user information page'),
			'description' => tra('Display UserTracker information on the user information page'),
			'help' => 'User+Tracker',
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'userTracker',
			),
		),
	);
}

