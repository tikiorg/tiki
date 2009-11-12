<?php

function prefs_main_list() {
	return array(
		'main_shadow_start' => array(
			'name' => tra('Main shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'main_shadow_end' => array(
			'name' => tra('Main shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
