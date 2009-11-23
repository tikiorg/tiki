<?php

function prefs_box_list() {
	return array(
		'box_shadow_start' => array(
			'name' => tra('Module (box) shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'box_shadow_end' => array(
			'name' => tra('Module (box) shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
