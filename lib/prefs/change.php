<?php

function prefs_change_list() {
	return array(
		'change_language' => array(
			'name' => tra('Users can change site language'),
			'description' => tra('Allow users to change the language of the menus and labels.'),
			'type' => 'flag',
		),
	);
}
