<?php

function prefs_max_list() {
	return array(
		'max_username_length' => array(
			'name' => tra('Maximum length:'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);	
}
