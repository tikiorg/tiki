<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_tag_info() {
	return array(
		'name' => tra('Tag'),
		'documentation' => 'PluginTag',
		'description' => tra('Apply an HTML tag to content'),
		'prefs' => array( 'wikiplugin_tag' ),
		'validate' => 'all',
		'body' => tra('text'),
		'icon' => 'pics/icons/script_code_red.png',
		'params' => array(
			'tag' => array(
				'required' => false,
				'name' => tra('Tag Name'),
				'description' => tra('Any valid HTML tag, span by default.'),
				'default' => 'span',
			),
			'style' => array(
				'required' => false,
				'name' => tra('CSS Style'),
				'description' => tra('Equivalent to the style attribute of an HTML tag.'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_tag($data, $params) {
	extract ($params,EXTR_SKIP);
	if (!isset($tag)) {
		$tag = 'span';
	} else {
		// remove eveyrything what's not a word to allow only tags without attributes
		$tag = preg_replace("/[^\w]/e", "", $tag);
	}
	
	if (isset($style)) {
		// trim quotes from the begin and end of style
		$style = ' style="'.trim($style,"\'\"").'"';
	} else {
		$style = '';
	}
	return "<$tag$style>$data</$tag>";
}
