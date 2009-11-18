<?php

function prefs_middle_list() {
	return array(
		'middle_shadow_start' => array(
			'name' => tra('Middle shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'middle_shadow_end' => array(
			'name' => tra('Middle shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
