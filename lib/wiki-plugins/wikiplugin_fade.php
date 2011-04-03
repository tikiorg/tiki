<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fade_info()
{
	return array(
		'name' => tra('Fade'),
		'documentation' => 'PluginFade',
		'description' => tra('Create a link that shows/hides initially hidden content'),
		'prefs' => array('wikiplugin_fade'),
		'body' => tra('Wiki syntax containing the content that can be hidden or shown.'),
		'filter' => 'wikicontent',
		'icon' => 'pics/icons/wand.png',
		'params' => array(
			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'filter' => 'striptags',
				'description' => tra('Label for link that shows and hides the content when clicked'),
				'default' => tra('Unspecified label')
			),
			'icon' => array(
				'required' => false,
				'name' => tra('Icon'),
				'filter' => 'alpha',
				'description' => tra('Arrow icon showing that content can be hidden or shown.'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
			),
			'show_speed' => array(
				'required' => false,
				'name' => tra('Show Speed'),
				'filter' => 'digits',
				'description' => tra('Speed of animation in milliseconds when showing content (200 is fast and 600 is slow. 1000 equals 1 second).'),
				'default' => 400,
				'advanced' => true,
			),
			'hide_speed' => array(
				'required' => false,
				'name' => tra('Hide Speed'),
				'filter' => 'digits',
				'description' => tra('Speed of animation in milliseconds when hiding content (200 is fast and 600 is slow. 1000 equals 1 second).'),
				'default' => 400,
				'advanced' => true,
			),
		)
	);
}

function wikiplugin_fade( $body, $params )
{
	static $id = 0;
	global $tikilib;
	//set defaults
	$plugininfo = wikiplugin_fade_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	//apply user parameter settings
	$params = array_merge($default, $params);
	
	if (!isset($params['label'])) {
		$params['label'] = tra('Unspecified label');
	}
	$unique = 'wpfade-' . ++$id;
	$unique_link = $unique . '-link';

	if ($params['icon'] == 'y') {
		$span_class = 'wpfade-span-icon';
		$a_class_hidden = '\'wpfade-hidden\'';
		$a_class_shown = '\'wpfade-shown\'';
		$div_class = 'wpfade-div-icon';
	} else {
		$span_class = 'wpfade-span-plain';
		$a_class_hidden = '';
		$a_class_shown = '';
		$div_class = 'wpfade-div-plain';
	}
		
	$body = trim($body);
	$body = $tikilib->parse_data( $body );
	$jq = '
				var icon = "'. $params['icon'] . '";
				$(document).ready(function(){
					$(\'#' . $unique_link . '\').toggle(
						function() {
							$(\'#' . $unique . '\').show(\'blind\', {}, ' . $params['show_speed'] . ');
							$(\'#' . $unique_link . '\').addClass(' . $a_class_shown . ').removeClass(' . $a_class_hidden . ');
						},
						function() {
							$(\'#' . $unique . '\').hide(\'blind\', {}, ' . $params['hide_speed'] . ');
							$(\'#' . $unique_link . '\').addClass(' . $a_class_hidden . ').removeClass(' . $a_class_shown . ');
						}
					);
					return false;
				});';
	global $headerlib;
	$headerlib->add_jq_onready($jq);
	//wrapping in an extra div makes animation smoother	
	return '~np~<div>' . "\r\t" . '<span class="' . $span_class . '">' . "\r\t\t" 
		. '<a id="' . $unique_link . '" class=' . $a_class_hidden . '>' . "\r\t\t\t" . $params['label'] . "\r\t\t" 
		. '</a>' . "\r\t" . '</span>' . "\r\t" . '<div id="' . $unique . '" class="' . $div_class . '">' . "\r\t\t\t" 
		. $body . "\r\t" . '</div>' . "\r" . '</div>' . "\r" . '~/np~';


}
