<?php

function prefs_jquery_list() {
	return array(
		'jquery_effect_speed' => array(
			'name' => tra('Speed'),
			'type' => 'list',
			'options' => array(
				'fast' => tra('Fast'),
				'normal' => tra('Normal'),
				'slow' => tra('Slow'),
			),
		),
		'jquery_effect_direction' => array(
			'name' => tra('Direction'),
			'type' => 'list',
			'options' => array(
				'vertical' => tra('Vertical'),
				'horizontal' => tra('Horizontal'),
				'left' => tra('Left'),
				'right' => tra('Right'),
				'up' => tra('Up'),
				'down' => tra('Down'),
			),
		),
		'jquery_effect_tabs_speed' => array(
			'name' => tra('Speed'),
			'type' => 'list',
			'options' => array(
				'fast' => tra('Fast'),
				'normal' => tra('Normal'),
				'slow' => tra('Slow'),
			),
		),
		'jquery_effect_tabs_direction' => array(
			'name' => tra('Direction'),
			'type' => 'list',
			'options' => array(
				'vertical' => tra('Vertical'),
				'horizontal' => tra('Horizontal'),
				'left' => tra('Left'),
				'right' => tra('Right'),
				'up' => tra('Up'),
				'down' => tra('Down'),
			),
		),
	

	// Used in templates/tiki-admin-include-look.tpl
	'jquery_effect' => array(
			'name' => tra('Effect for modules'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'jquery_effect_tabs' => array(
			'name' => tra('Effect for tabs'),
			'type' => '',
			),
	
	);	
}
