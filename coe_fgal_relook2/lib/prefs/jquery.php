<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_jquery_list($partial = false) {

	global $prefs;

	$jquery_effect_options = array(
		''      => tra('Default'),
		'none'  => tra('None'),
		'slide' => tra('Slide'),
		'fade'  => tra('Fade'),
	);	

	if (! $partial && $prefs['feature_jquery_ui'] == 'y') {
		$jquery_effect_options['blind_ui'] = tra('Blind (UI)');
		$jquery_effect_options['clip_ui'] = tra('Clip (UI)');
		$jquery_effect_options['drop_ui'] = tra('Drop (UI)');
		$jquery_effect_options['explode_ui'] = tra('Explode (UI)');
		$jquery_effect_options['fold_ui'] = tra('Fold (UI)');
		$jquery_effect_options['puff_ui'] = tra('Puff (UI)');
		$jquery_effect_options['slide_ui'] = tra('Slide (UI)');
	}

	return array(
		'jquery_effect' => array(
			'name' => tra('Effect for modules'),
			'type' => 'list',
			'options' => $jquery_effect_options,
			'help' => 'JQuery#Effects',
			'default' => '',				// Default effect for general show/hide: ['' | 'slide' | 'fade' | and
											// see http://docs.jquery.com/UI/Effects: 'blind' | 'clip' | 'explode' etc]
		),
		'jquery_effect_tabs' => array(
			'name' => tra('Effect for tabs'),
			'type' => 'list',
			'options' => $jquery_effect_options,
			'help' => 'JQuery#Effects',
			'default' => 'slide',	// Different effect for tabs (['none' | 'normal' (for jq) | 'slide' etc]
		),
		'jquery_effect_speed' => array(
			'name' => tra('Speed'),
			'type' => 'list',
			'options' => array(
				'fast' => tra('Fast'),
				'normal' => tra('Normal'),
				'slow' => tra('Slow'),
			),
			'default' => 'normal', 	// ['slow' | 'normal' | 'fast' | milliseconds (int) ]
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
			'default' => 'vertical', 	// ['horizontal' | 'vertical' | 'left' | 'right' | 'up' | 'down' ]
		),
		'jquery_effect_tabs_speed' => array(
			'name' => tra('Speed'),
			'type' => 'list',
			'options' => array(
				'fast' => tra('Fast'),
				'normal' => tra('Normal'),
				'slow' => tra('Slow'),
			),
			'default' => 'fast',
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
			'default' => 'vertical',
		),
	);	
}
