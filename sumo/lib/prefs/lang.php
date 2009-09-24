<?php

function prefs_lang_list() {
	return array(
		'lang_use_db' => array(
			'name' => tra('Use database for translation'),
			'description' => tra('Verify for available translations in the database.'),
			'type' => 'flag',
			'help' => 'Translating+Tiki+interface',
		),
	);
}
