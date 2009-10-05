<?php

function prefs_show_list() {
	return array(
		'show_available_translations' => array(
			'name' => tra('Display available translations'),
			'description' => tra('?'),
			'type' => 'flag',
		),
	);
}

