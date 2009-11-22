<?php

function prefs_w_list() {
	return array(
		'w_displayed_default' => array(
			'name' => 'Display by default',
			'type' => 'flag',
		),

	// Used in templates/tiki-admin-include-wiki.tpl
	'w_use_db' => array(
			'name' => '',
			'type' => '',
			),
	// Used in templates/tiki-admin-include-wiki.tpl
	'w_use_dir' => array(
			'name' => tra('Path'),
			'type' => '',
			),
	
	);	
	
}
