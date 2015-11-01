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
		'iconname' => 'code',
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'validate' => 'arguments',
		'introduced' => 1,
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tr('Indicate the type of HTML tag to use (default is %0)', '<code>div</code>'),
				'since' => '1',
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
				'since' => '1',
				'filter' => 'text',
				'accepted' => tra('Valid CSS color name or hex code'),
				'safe' => true,
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Box width'),
				'description' => tra('In pixels or percentage. Default is original size'),
				'since' => '1',
				'default' => '',
				'filter' => 'text',
				'safe' => true,
			),
			'align' => array(
				'required' => false,
				'name' => tra('Text Alignment'),
				'description' => tra('Aligns the text within the element'),
				'since' => '1',
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
				'description' => tr('Set the alignment for the entire element. For elements with a width of less than
				100%, other elements will wrap around it unless the %0 parameter is appropriately set.', '<code>clear</code>'
				),
				'since' => '1',
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
				'since' => '1',
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
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('HTML ID'),
				'description' => tra('Sets the div\'s id attribute, as defined by HTML.'),
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			),
			'title' => array(
				'required' => false,
				'name' => tra('Title attribute'),
				'description' => tra('Title for the div, usually displayed as a tooltip.'),
				'since' => '9.2',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			),
			'onclick' => array(
				'required' => false,
				'name' => tra('onClick attribute'),
				'description' => tra('Enter on onclick event'),
				'filter' => 'text',
				'advanced' => true,
				'since' => '14.0',
				'default' => '',
			),
			'style' => array(
				// Note that this is ignored unless preference wiki_plugindiv_approvable is set in
				// Configuration → Configuration Panels → Editing and Plugins → Miscellaneous
				'required' => false,
				'name' => tra('Style attribute'),
				'description' => tra('Enter CSS styling tags for the div type used.'),
				'since' => '13.0',
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
	if ($prefs['wiki_plugindiv_approvable'] != 'y' || !isset($style)) {
		// If any other unsafe parameters are created, unset them here
		$style = '';
	}
	$possibletypes = array('div','span','pre','b','i','tt','p','blockquote');
	$t    = (isset($type) and in_array($type, $possibletypes)) ? "$type"  : "div";
	$c    = (isset($class)) ? " class='$class'"  : "";
	$id   = (isset($id)) ? " id='$id'"  : "";
	$oc   = (isset($onclick)) ? " onclick='$onclick'"  : "";
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
	$begin .= " $c $id $oc>";
	$end = "</$t>";
	return $begin . $data . $end;
}
