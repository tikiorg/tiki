<?php

function prefs_footer_list() {
	return array(
		'footer_shadow_start' => array(
			'name' => tra('Footer shadow start'),
			'type' => 'textarea',
			'size' => '2',
		),
		'footer_shadow_end' => array(
			'name' => tra('Footer shadow end'),
			'type' => 'textarea',
			'size' => '2',
		),
	);	
}
