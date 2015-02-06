<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_div_info()
{
	global $prefs;

	$info = array(
		'name' => tra('Div'),
		'documentation' => 'PluginDiv',
		'description' => tra('Define and format sections of a page or text'),
		'prefs' => array('wikiplugin_div'),
		'body' => tra('text'),
		'icon' => 'img/icons/script_code_red.png',
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'validate' => 'arguments',
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Indicate the type of HTML tag to use (default is div)'),
				'filter' => 'alpha',
				'safe' => true,
				'default' => 'div',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Div'), 'value' => 'div'),
					array('text' => tra('Span'), 'value' => 'span'),
					array('text' => tra('Pre'), 'value' => 'pre'),
					array('text' => tra('Bold'), 'value' => 'b'),
					array('text' => tra('Italic'), 'value' => 'i'),
					array('text' => tra('Teletype'), 'value' => 'tt'),
					array('text' => tra('Paragraph'), 'value' => 'p'),
					array('text' => tra('Block quote'), 'value' => 'blockquote'),
				),
			),
			'bg' => array(
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('As defined by CSS, name or Hex code.'),
				'filter' => 'striptags',
				'safe' => true,
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Box width'),
				'description' => tra('In pixels or percentage. Default is original size'),
				'default' => '',
				'filter' => 'text',
				'safe' => true,
			),
			'align' => array(
				'required' => false,
				'name' => tra('Text Alignment'),
				'description' => tra('Aligns the text within the element'),
				'filter' => 'alpha',
				'safe' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Center'), 'value' => 'center'),
					array('text' => tra('Justify'), 'value' => 'justify'),
				),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float Position'),
				'description' => tra(
					'Set the alignment for the entire element. For elements with a width of less than 100%, other elements will wrap around it
					unless the clear parameter is appropriately set.'
				),
				'filter' => 'alpha',
				'safe' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('None'), 'value' => 'none'),
				),
			),
			'clear' => array(
				'required' => false,
				'name' => tra('Clear'),
				'description' => tra('Items are not allowed to wrap around the side(s) this parameter is set to.'),
				'filter' => 'text',
				'safe' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Both'), 'value' => 'both'),
					array('text' => tra('None'), 'value' => 'none'),
				),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the div.'),
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('HTML ID'),
				'description' => tra('Sets the div\'s id attribute, as defined by HTML.'),
				'filter' => 'striptags',
				'safe' => true,
				'default' => '',
			),
			'title' => array(
				'required' => false,
				'name' => tra('Title attribute'),
				'description' => tra('Title for the div, usually displayed as a tooltip.'),
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			),
			'style' => array(
				// Note that this is ignored unless preference wiki_plugindiv_approvable is set in Configuration → Configuration Panels → Editing and Plugins → Miscellaneous
				'required' => false,
				'name' => tra('Style attribute'),
				'description' => tra('Enter CSS styling tags for the div type used.'),
				'filter' => 'text',
				'advanced' => true,
				'default' => '',
			),

		),
	);

	if ($prefs['wiki_plugindiv_approvable'] != 'y') {
		unset($info['validate']);
		// If any other unsafe parameters are created, unset them here
		unset($info['params']['style']);
	}

  return $info;

}

function wikiplugin_div($data, $params)
{
	global $prefs;

	extract($params, EXTR_SKIP);
	if ($prefs['wiki_plugindiv_approvable'] != 'y') {
		// If any other unsafe parameters are created, unset them here
		$style = '';
	}
	$possibletypes = array('div','span','pre','b','i','tt','p','blockquote');
	$t    = (isset($type) and in_array($type, $possibletypes)) ? "$type"  : "div";
	$c    = (isset($class)) ? " class='$class'"  : "";
	$id    = (isset($id)) ? " id='$id'"  : "";
	$w    = (isset($width)) ? " width: $width;"  : "";
	$bg   = (isset($bg))    ? " background-color: $bg;" : "";
	$al   = (isset($align) && ($align == 'right' || $align == "center" || $align == "justify" || $align == 'left')) ? " text-align: $align;" : '';
	$fl   = (isset($float) && ($float == 'left' || $float == 'right' || $float == 'none')) ? " float: $float;"  : '';
	$cl   = (isset($clear) && ($clear == 'left' || $clear == 'right' || $clear == 'both' || $clear == 'none')) ? " clear: $clear;"  : '';

	if (!empty($title)) {
		$title = " title=\"$title\"";
	} else {
		$title = '';
	}
	$begin  = "<{$t}{$title}";
	$format = "$style$bg$al$w$fl$cl";
	if (!empty($format)) {
		$begin .= " style=\"$format\"";
	}
	$begin .= " $c $id>";
	$end = "</$t>";
	return $begin . $data . $end;
}
