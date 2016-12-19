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
		),
	);
}

function wikiplugin_shorten($data, $params)
{
	global $prefs;
	static $shorten_count;

	$defaults = array(
		'length' => 20,
		'moreText' => '...',
		'lessText' => '[-]',
	);

	$length = isset($params['length']) ? $params['length'] : $defaults['length'];
	$length = (int) sprintf('%d', $length);
	$length = max(1, $length);

	$match = null;
	if ( preg_match('/^\s*.{'.$length.'}[^\s]*/', $data, $match) ) {

		$out = '';
		if(!$shorten_count) {
			$out .= '<style type="text/css">';
			$out .= ' .toggle_shorten_text_wrapper { display: none; }';
			$out .= ' .toggle_shorten_text_button:checked + .toggle_shorten_text_wrapper { display: inline; }';
			$out .= ' .toggle_shorten_text_button ~ .toggle_shorten_text_more { display: inline; }';
			$out .= ' .toggle_shorten_text_button ~ .toggle_shorten_text_less { display: none; }';
			$out .= ' .toggle_shorten_text_button:checked ~ .toggle_shorten_text_more { display: none; }';
			$out .= ' .toggle_shorten_text_button:checked ~ .toggle_shorten_text_less { display: inline; }';
			$out .= ' .toggle_shorten_text_more, .toggle_shorten_text_less { cursor: pointer; margin-left: 3px; }';
			$out .= '</style>';
		}

		$id = 'toggle_shorten_text-' . ++$shorten_count;

		$moreText = isset($params['moreText']) ? $params['moreText'] : $defaults['moreText'];
		$moreText = strip_tags($moreText);

		$lessText = isset($params['lessText']) ? $params['lessText'] : $defaults['lessText'];
		$lessText = strip_tags($lessText);

		$html = '<span>';
		$html .= '<input style="display: none" class="toggle_shorten_text_button" type="checkbox" id="'.$id.'"/>';
		$html .= '<span class="toggle_shorten_text_wrapper">%s</span>';
		$html .= '<label class="toggle_shorten_text_more" for="'.$id.'">'.$moreText.'</label>';
		$html .= '<label class="toggle_shorten_text_less" for="'.$id.'">'.$lessText.'</label>';
		$html .= '</span>';

		$index = strlen($match[0]);
		$out .= substr($data, 0, $index);
		$out .= sprintf($html, substr($data, $index));

		return $out;
	}

	return $data;
}
