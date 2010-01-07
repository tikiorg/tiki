<?php

function prefs_center_list() {
	return array(
		'center_shadow_start' => array(
			'name' => tra('Center shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'center_shadow_end' => array(
			'name' => tra('Center shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
