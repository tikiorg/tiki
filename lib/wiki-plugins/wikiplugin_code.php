<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_code_info()
{
	return array(
		'name' => tra('Code'),
		'documentation' => 'PluginCode',
		'description' => tra('Display code syntax with line numbers and color highlights'),
		'prefs' => array('wikiplugin_code'),
		'body' => tra('Code'),
		'icon' => 'pics/icons/page_white_code.png',
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),	
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Code snippet label.'),
			),
			'wrap' => array(
				'required' => false,
				'name' => tra('Word Wrap'),
				'description' => tra('Enable word wrapping on the code to avoid breaking the layout.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => 'y'
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Available: php, html, sql, javascript, css, java, c, doxygen, delphi, rsplus...'),
				'advanced' => false,
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line Numbers'),
				'description' => tra('Show line numbers for each line of code. May not be used with colors unless GeSHI is installed.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'advanced' => true,
			),
			'rtl' => array(
				'required' => false,
				'name' => tra('Right to Left'),
				'description' => tra('Switch the text display from left to right to right to left  (left to right by default)'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_code($data, $params)
{
	global $prefs;
	static $code_count;
	
	$defaults = array(
		'wrap' => 'y'
	);
	
	$params = array_merge($defaults, $params);
	
	extract($params, EXTR_SKIP);

	$code = trim($data);
	$code = str_replace('&lt;x&gt;', '', $code);
	$code = str_replace('<x>', '', $code);

	$parse_wiki = ( isset($wiki) && $wiki == 1 );
	$id = 'codebox'.++$code_count;
	$boxid = " id=\"$id\" ";

	$out = $code;

	if (isset($colors) && $colors == '1') {	// remove old geshi setting as it upsets codemirror
		unset( $colors );
	}

	$out = (isset($caption) ? '<div class="codecaption">'.$caption.'</div>' : "" ) 
		. '<pre class="codelisting" '
		. (isset($colors) ? ' data-syntax="' . $colors . '" ' : '')
		. (isset($ln) ? ' data-line-numbers="' . $ln . '" ' : '')
		. (isset($wrap) ? ' data-wrap="' . $wrap . '" ' : '')
		. ' dir="'.( (isset($rtl) && $rtl == 1) ? 'rtl' : 'ltr') . '" '
		. (isset($pre_style) ? ' style="'.$pre_style.'"' : '')
		. $boxid.'>'
		. '~np~'
		. $out
		. '~/np~'
		. '</pre>';

	return $out;
}

