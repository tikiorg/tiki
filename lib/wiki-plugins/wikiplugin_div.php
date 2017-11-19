<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_div_info()
{
	global $prefs;

	$info = [
		'name' => tra('Div'),
		'documentation' => 'PluginDiv',
		'description' => tra('Define and format sections of a page or text'),
		'prefs' => ['wikiplugin_div'],
		'body' => tra('text'),
		'iconname' => 'code',
		'filter' => 'wikicontent',
		'tags' => [ 'basic' ],
		'validate' => 'arguments',
		'introduced' => 1,
		'params' => [
			'type' => [
				'required' => false,
				'name' => tra('Type'),
				'description' => tr('Indicate the type of HTML tag to use (default is %0)', '<code>div</code>'),
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => 'div',
				'options' => [
					['text' => tra('None'), 'value' => ''],
					['text' => tra('Div'), 'value' => 'div'],
					['text' => tra('Span'), 'value' => 'span'],
					['text' => tra('Pre'), 'value' => 'pre'],
					['text' => tra('Bold'), 'value' => 'b'],
					['text' => tra('Italic'), 'value' => 'i'],
					['text' => tra('Teletype'), 'value' => 'tt'],
					['text' => tra('Paragraph'), 'value' => 'p'],
					['text' => tra('Block quote'), 'value' => 'blockquote'],
					['text' => tra('H1'), 'value' => 'h1'],
					['text' => tra('H2'), 'value' => 'h2'],
					['text' => tra('H3'), 'value' => 'h3'],
					['text' => tra('H4'), 'value' => 'h4'],
					['text' => tra('H5'), 'value' => 'h5'],
					['text' => tra('H6'), 'value' => 'h6'],
				],
			],
			'bg' => [
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('As defined by CSS, name, or color hex code.'),
				'since' => '1',
				'filter' => 'text',
				'accepted' => tra('Valid CSS color name or hex code'),
				'safe' => true,
				'default' => '',
			],
			'width' => [
				'required' => false,
				'name' => tra('Box width'),
				'description' => tra('In pixels or percentage. Default is original size'),
				'since' => '1',
				'default' => '',
				'filter' => 'text',
				'safe' => true,
			],
			'align' => [
				'required' => false,
				'name' => tra('Text Alignment'),
				'description' => tra('Aligns the text within the element'),
				'since' => '1',
				'filter' => 'alpha',
				'safe' => true,
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Center'), 'value' => 'center'],
					['text' => tra('Justify'), 'value' => 'justify'],
				],
			],
			'float' => [
				'required' => false,
				'name' => tra('Float Position'),
				'description' => tr('Set the alignment for the entire element. For elements with a width of less than
				100%, other elements will wrap around it unless the %0 parameter is appropriately set.', '<code>clear</code>'),
				'since' => '1',
				'filter' => 'alpha',
				'safe' => true,
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
			'clear' => [
				'required' => false,
				'name' => tra('Clear'),
				'description' => tra('Content cannot wrap around this object because of what the parameter is set to.'),
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Both'), 'value' => 'both'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
			'class' => [
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the div.'),
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			],
			'id' => [
				'required' => false,
				'name' => tra('HTML ID'),
				'description' => tra("Sets the id attribute of the div, as defined by HTML."),
				'since' => '1',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			],
			'title' => [
				'required' => false,
				'name' => tra('Title attribute'),
				'description' => tra('Title for the div, usually displayed as a tooltip.'),
				'since' => '9.2',
				'filter' => 'text',
				'safe' => true,
				'default' => '',
			],
			'onclick' => [
				'required' => false,
				'name' => tra('onClick attribute'),
				'description' => tra('Enter on onclick event'),
				'filter' => 'text',
				'advanced' => true,
				'since' => '14.0',
				'default' => '',
			],
			'style' => [
				// Note that this is ignored unless preference wiki_plugindiv_approvable is set in
				// Configuration → Configuration Panels → Editing and Plugins → Miscellaneous
				'required' => false,
				'name' => tra('Style attribute'),
				'description' => tra('Enter CSS styling tags for the div type used.'),
				'since' => '13.0',
				'filter' => 'text',
				'advanced' => true,
				'default' => '',
			],

		],
	];

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
	if ($prefs['wiki_plugindiv_approvable'] != 'y' || ! isset($style)) {
		// If any other unsafe parameters are created, unset them here
		$style = '';
	}
	$possibletypes = ['div','span','pre','b','i','tt','p','blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
	$t    = (isset($type) and in_array($type, $possibletypes)) ? "$type" : "div";
	$c    = (isset($class)) ? " class='$class'" : "";
	$id   = (isset($id)) ? " id='$id'" : "";
	$oc   = (isset($onclick)) ? " onclick='$onclick'" : "";
	$w    = (isset($width)) ? " width: $width;" : "";
	$bg   = (isset($bg)) ? " background-color: $bg;" : "";
	$al   = (isset($align) && ($align == 'right' || $align == "center" || $align == "justify" || $align == 'left')) ? " text-align: $align;" : '';
	$fl   = (isset($float) && ($float == 'left' || $float == 'right' || $float == 'none')) ? " float: $float;" : '';
	$cl   = (isset($clear) && ($clear == 'left' || $clear == 'right' || $clear == 'both' || $clear == 'none')) ? " clear: $clear;" : '';

	if (! empty($title)) {
		$title = " title=\"$title\"";
	} else {
		$title = '';
	}
	$begin  = "<{$t}{$title}";
	$format = "$style$bg$al$w$fl$cl";
	if (! empty($format)) {
		$begin .= " style=\"$format\"";
	}
	$begin .= " $c $id $oc>";
	$end = "</$t>";
	return $begin . $data . $end;
}
