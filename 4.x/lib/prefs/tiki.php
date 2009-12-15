<?php

function prefs_tiki_list() {
	return array(
		'tiki_version_check_frequency' => array(
			'name' => tra('Check frequency'),
			'type' => 'list',
			'options' => array(
				'86400' => tra('Each day'),
				'604800' => tra('Each week'),
				'2592000' => tra('Each month'),
			),
			'dependencies' => array(
				'feature_version_checks',
			),
		),
		'tiki_minify_javascript' => array(
			'name' => tra('Minify javascript'),
			'description' => tra('Compress javascript files used in the page into a single file to be distributed statically. Changes to javascript files will require cache to be cleared.'),
			'type' => 'flag',
		),
	);
}
