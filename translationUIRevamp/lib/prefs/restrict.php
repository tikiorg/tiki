<?php

function prefs_restrict_list() {
	return array(
		'restrict_language' => array(
			'name' => tra('Restrict supported languages'),
			'description' => tra('Limit the languages made available on the site.'),
			'type' => 'flag',
		),
	);
}

