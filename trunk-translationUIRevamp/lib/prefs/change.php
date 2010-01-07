<?php

function prefs_change_list() {
	return array(
		'change_language' => array(
			'name' => tra('Users can change site language'),
			'description' => tra('Allow users to change the language of the menus and labels.'),
			'type' => 'flag',
		),
		'change_theme' => array(
			'name' => tra('Users can change theme'),
			'type' => 'flag',
		),
		'change_password' => array(
			'name' => tra('Users can change their password'),
			'type' => 'flag',
			'help' => 'User+Preferences',
		),
	);
}
