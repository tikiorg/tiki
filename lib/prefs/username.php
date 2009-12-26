<?php

function prefs_username_list() {
	return array(
		'username_pattern' => array(
			'name' => tra('Username pattern'),
			'type' => 'text',
			'size' => 25,
			'perspective' => false,
		),
	);	
}
