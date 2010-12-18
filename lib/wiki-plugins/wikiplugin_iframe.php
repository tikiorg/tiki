<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 *
 * IFRAME plugin. Creates an iframe and loads the specified page within the frame.
 *
 * Syntax:
 *
 *  {IFRAME(some parameters)}$data{IFRAME}
 *
 * Syntax:
 *
 * {IFRAME(name=>name, longdescription=>, width=>, height=>, align=>, frameborder=>, marginheight=> marginwidth=> scrolling=>)}source_URL{IFRAME}
 *
 */
function wikiplugin_iframe_help() {
	return tra("iframe").":~np~{IFRAME(name=xxx, width=100, height=100, align=top|middle|bottom|left|right, frameborder=1|0, marginheight=0, marginwidth=0, scrolling=auto)}".tra('URL')."{IFRAME}~/np~";
}

function wikiplugin_iframe_info() {
	return array(
		'name' => tra('Iframe'),
		'documentation' => 'PluginIframe',
		'description' => tra('Include another web page within a frame'),
		'prefs' => array( 'wikiplugin_iframe' ),
		'body' => tra('URL'),
		'validate' => 'all',
		'params' => array(
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name'),
				'default' => '',
			),
			'title' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Frame title'),
				'default' => '',
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or %'),
				'default' => '',
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
				'default' => '',
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Align the ifram on the page'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Middle'), 'value' => 'middle'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right') 
				)
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Frame Border'),
				'description' => tra('Choose whether to show a border around the iframe'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Margin height in pixels'),
				'default' => '',
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Margin width in pixels'),
				'default' => '',
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => tra('Choose whether to add a scroll bar'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'yes'), 
					array('text' => tra('No'), 'value' => 'no'),
					array('text' => tra('Auto'), 'value' => 'auto'),
				)
			),
			'src' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL'),
				'default' => '',
			),
		), 
	);
}

function wikiplugin_iframe($data, $params) {

	extract ($params, EXTR_SKIP);
	$ret = '<iframe ';

	if (isset($name)) {
		$ret .= " name=\"$name\"";
	}
	if (isset($title)) {
		$ret .= " title=\"$title\"";
	}
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	}
	if (isset($align)) {
		$ret .= " align=\"$align\"";
	}
	if (isset($frameborder)) {
		$ret .= " frameborder=\"$frameborder\"";
	}
	if (isset($marginheight)) {
		$ret .= " marginheight=\"$marginheight\"";
	}
	if (isset($marginwidth)) {
		$ret .= " marginwidth=\"$marginwidth\"";
	}
	if (isset($scrolling)) {
		$ret .= " scrolling=\"$scrolling\"";
	}
	if (isset($src)) {
		$ret .= " src=\"$src\"";
	} elseif (!empty($data)) {
		$ret .= " src=\"$data\"";
	}
	$ret .= ">$data</iframe>";
	return $ret;
}
