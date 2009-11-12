<?php

function prefs_header_list() {
	return array(
		'header_shadow_start' => array(
			'name' => tra('Header shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'header_shadow_end' => array(
			'name' => tra('Header shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
