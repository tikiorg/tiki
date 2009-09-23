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
	);
}
