<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_code_info()
{
	return array(
		'name' => tra('Code'),
		'documentation' => 'PluginCode',
		'description' => tra('Display code with syntax highlighting and line numbering'),
		'prefs' => array('wikiplugin_code'),
		'body' => tra('Code to be displayed'),
		'iconname' => 'code',
		'introduced' => 1,
		'filter' => 'rawhtml_unsafe',
		'format' => 'html',
		'tags' => array( 'basic' ),
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Code snippet label.'),
				'since' => '1',
				'filter' => 'text',
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
				'filter' => 'digits',
				'default' => '1'
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Any supported language listed at http://codemirror.net/mode/'),
				'since' => '1',
				'advanced' => false,
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line Numbers'),
				'description' => tra('Show line numbers for each line of code.'),
				'since' => '1',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'filter' => 'digits',
				'advanced' => true,
			),
			'rtl' => array(
				'required' => false,
				'name' => tra('Right to Left'),
				'description' => tra('Switch the text display from left to right, to right to left (left to right by default)'),
				'since' => '1',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'filter' => 'digits',
				'advanced' => true,
			),
			'mediawiki' => array(
				'required' => false,
				'name' => tra('Code Tag'),
				'description' => tra('Encloses the code in an HTML code tag, for example: &lt;code&gt;user input&lt;code&gt;'),
				'since' => '8.3',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'filter' => 'digits',
				'default' => '0',
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
		'wrap' => '1',
		'mediawiki' => '0',
		'ishtml' => false
	);
	
	$params = array_merge($defaults, $params);
	
	extract($params, EXTR_SKIP);
	$code = trim($data);
	if ($mediawiki =='1') {
		return "<code>$code</code>";
	}

	$code = str_replace('&lt;x&gt;', '', $code);
	$code = str_replace('<x>', '', $code);

	$id = 'codebox'.++$code_count;
	$boxid = " id=\"$id\" ";
	
	$out = $code;
	
	if (isset($colors) && $colors == '1') {	// remove old geshi setting as it upsets codemirror
		unset( $colors );
	}
	
	//respect wrap setting when Codemirror is off and set to wrap when Codemirror is on to avoid broken view while
	//javascript loads
	if ((isset($prefs['feature_syntax_highlighter']) && $prefs['feature_syntax_highlighter'] == 'y') || $wrap == 1) {
		$pre_style = 'white-space:pre-wrap;'
		.' white-space:-moz-pre-wrap !important;'
		.' white-space:-pre-wrap;'
		.' white-space:-o-pre-wrap;'
		.' word-wrap:break-word;';
	}

	$out = (isset($caption) ? '<div class="codecaption">'.$caption.'</div>' : "" )
		. '<pre class="codelisting" '
		. (isset($colors) ? ' data-syntax="' . $colors . '" ' : '')
		. (isset($ln) ? ' data-line-numbers="' . $ln . '" ' : '')
		. (isset($wrap) ? ' data-wrap="' . $wrap . '" ' : '')
		. ' dir="'.( (isset($rtl) && $rtl == 1) ? 'rtl' : 'ltr') . '" '
		. (isset($pre_style) ? ' style="'.$pre_style.'"' : '')
		. $boxid.'>'
		. (TikiLib::lib('parser')->option['ck_editor'] || $ishtml ? $out : htmlentities($out, ENT_QUOTES, 'UTF-8'))
		. '</pre>';

	return $out;
}

