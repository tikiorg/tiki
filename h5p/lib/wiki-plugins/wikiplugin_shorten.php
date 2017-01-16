<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_shorten_info()
{
	return array(
		'name' => tra('Shorten'),
		'documentation' => 'Shorten',
		'description' => tra('Show/hide a portion of text'),
		'prefs' => array('wikiplugin_shorten'),
		'body' => tra('Code to be displayed'),
		'iconname' => 'shorten',
		'introduced' => 17,
		'filter' => 'rawhtml_unsafe',
		'format' => 'html',
		'tags' => array( 'basic' ),
		'params' => array(
			'length' => array(
				'required' => true,
				'name' => tra('Length'),
				'description' => tra('Char length of always visible portion of text.'),
				'since' => '17',
				'filter' => 'digits',
			),
			'moreText' => array(
				'required' => false,
				'name' => tra('More text'),
				'description' => tra('A label indicating the text is expandable.'),
				'since' => '17',
				'filter' => 'text',
			),
			'lessText' => array(
				'required' => false,
				'name' => tra('Less text'),
				'description' => tra('A label for collapse text action.'),
				'since' => '17',
				'filter' => 'text',
			),
			'show_speed' => array(
				'required' => false,
				'name' => tra('Show Speed'),
				'filter' => 'alnum',
				'description' => tr('Speed of animation in milliseconds when showing content (%0200%1 is fast and
					%0600%1 is slow. %01000%1 equals 1 second).', '<code>', '</code>'),
				'default' => 0,
				'since' => '17',
				'accepted' => tr('Integer greater than 0 and less than or equal to 1000, or %0 or %1',
					'<code>fast</code>', '<code>slow</code>'),
				'advanced' => true,
			),
			'hide_speed' => array(
				'required' => false,
				'name' => tra('Hide Speed'),
				'filter' => 'alnum',
				'description' => tr('Speed of animation in milliseconds when hiding content (%0200%1 is fast and
					%0600%1 is slow. %01000%1 equals 1 second).', '<code>', '</code>'),
				'default' => 0,
				'since' => '17',
				'accepted' => tr('Integer greater than 0 and less than or equal to 1000, or %0 or %1',
					'<code>fast</code>', '<code>slow</code>'),
				'advanced' => true,
			),

		),
	);
}

function wikiplugin_shorten($data, $params)
{
	global $prefs;
	static $shorten_count;
	$headerlib = TikiLib::lib('header');

	extract(array(
		'length' => 20,
		'moreText' => '[+]',
		'lessText' => '[-]',
		'show_speed' => 0,
		'hide_speed' => 0,
	));

	if (isset($params['length'])) {
		$length = (int) sprintf('%d', $params['length']);
		$length = max(1, $length);
	}

	if (isset($params['show_speed'])) {
		$show_speed = str_replace(array('slow', 'fast'), array('600', '200'), $params['show_speed']);
		$show_speed = (int) sprintf('%d', $show_speed);
	}

	if (isset($params['hide_speed'])) {
		$hide_speed = str_replace(array('slow', 'fast'), array('600', '200'), $params['hide_speed']);
		$hide_speed = (int) sprintf('%d', $hide_speed);
	}

	$match = null;
	if ( preg_match('/^\s*.{'.$length.'}[^\s]*/', $data, $match) ) {
		if(!$shorten_count) {
			$headerlib->add_css(
				" .toggle_shorten_text_button ~ .toggle_shorten_text_more { display: inline; }"
				. " .toggle_shorten_text_button ~ .toggle_shorten_text_less { display: none; }"
				. " .toggle_shorten_text_button:checked ~ .toggle_shorten_text_more { display: none; }"
				. " .toggle_shorten_text_button:checked ~ .toggle_shorten_text_less { display: inline; }"
				. " .toggle_shorten_text_more, .toggle_shorten_text_less { cursor: pointer; margin-left: 3px; }"
			);
		}

		$shorten_count += 1;

		$span_id = 'toggle_shorten_text_span-' . $shorten_count;
		$button_id = 'toggle_shorten_text_button-' . $shorten_count;

		$headerlib->add_css(
			 "#{$span_id} .toggle_shorten_text_wrapper { font-size: 0; transition: font-size {$hide_speed}ms linear; }"
			. "#{$span_id} .toggle_shorten_text_button:checked + .toggle_shorten_text_wrapper { font-size: inherit; transition-duration: {$show_speed}ms; }"
		);


		if(isset($params['moreText'])) {
			$moreText = strip_tags($params['moreText']);
		}

		if(isset($params['lessText'])) {
			$lessText = strip_tags($params['lessText']);
		}

		$html = '<span id="' . $span_id . '">';
		$html .= '<input style="display: none" class="toggle_shorten_text_button" type="checkbox" id="'.$button_id.'"/>';
		$html .= '<span class="toggle_shorten_text_wrapper">%s</span>';
		$html .= '<label class="toggle_shorten_text_more" for="'.$button_id.'">'.$moreText.'</label>';
		$html .= '<label class="toggle_shorten_text_less" for="'.$button_id.'">'.$lessText.'</label>';
		$html .= '</span>';

		$index = strlen($match[0]);
		$out = substr($data, 0, $index);
		$out .= sprintf($html, substr($data, $index));

		return $out;
	}

	return $data;
}
