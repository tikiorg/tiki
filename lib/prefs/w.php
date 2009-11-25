<?php

function prefs_w_list() {
	return array(
		'w_displayed_default' => array(
			'name' => 'Display by default',
			'type' => 'flag',
		),
		'w_use_dir' => array(
			'name' => tra('Path (if stored in directory)'),
			'type' => 'text',
			'size' => '20',
		),
		'w_use_db' => array(
			'type' => 'radio',
			'options' => array(
				'y' => tra('Store in database'),
				'n' => tra('Store in directory'),
			),
		),
	);	
	
}
