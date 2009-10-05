<?php

function prefs_load_list() {
	return array (
		'load_threshold' => array(
			'name' => tra('Maximum average server load threshold in the last minute'),
			'description' => tra('Maximum average server load threshold in the last minute'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '3',
			'dependencies' => array(
				'use_load_threshold',
			),
		),
	);
}
