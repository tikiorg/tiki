<?php

function prefs_babelfish_list() {
	return array(
		'babelfish_logo' => array(
			'name' => tra('Translation icons'),
			'description' => tra('Show clickable icons to translate the page to another language using Babelfish website.'),
			'type' => 'flag',
		),
	);
}