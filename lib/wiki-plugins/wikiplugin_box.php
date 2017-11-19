<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_box_info()
{
	return [
		'name' => tra('Box'),
		'documentation' => 'PluginBox',
		'description' => tra('Create a formatted box with a title bar'),
		'prefs' => ['wikiplugin_box'],
		'body' => tra('text'),
		'introduced' => 1,
		'iconname' => 'box',
		'tags' => [ 'basic' ],
				'validate' => 'arguments',
		'params' => [
			'title' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Box Title'),
				'description' => tra('Displayed above the content'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			],
			'bg' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Background Color'),
				'description' => tra('As defined by CSS, name, or color hex code.'),
				'since' => '1',
				'filter' => 'text',
				'accepted' => tra('Valid CSS color name or code'),
			],
			'width' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Box Width'),
				'description' => tr('In pixels or percentage. Default value is %0.', '<code>100%</code>'),
				'since' => '1',
				'filter' => 'text',
			],
			'align' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Text Alignment'),
				'description' => tra('Aligns the text within the box (left-aligned by default)'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'left',
				'options' => [
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('Center'), 'value' => 'center'],
				],
			],
			'float' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Float Position'),
				'description' => tr(
					'Set the alignment for the entire box. For elements with a width of less than 100%, other elements
					will wrap around it unless the %0 parameter is appropriately set.)',
					'<code>clear</code>'
				),
				'since' => '1',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Right'), 'value' => 'right'],
				],
			],
			'clear' => [
				'required' => false,
				'safe' => true,
				'name' => tra('Clear'),
				'description' => tr(
					'Text, etc. is not allowed to wrap around the box if this parameter is set to %0 (Yes)',
					'<code>1</code>'
				),
				'since' => '1',
				'filter' => 'digits',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 1],
					['text' => tra('No'), 'value' => 0]
				],
			],
			'class' => [
				'required' => false,
				'safe' => true,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the box.'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
				'accepted' => tra('Valid CSS class'),
			],
			'style' => [
				'required' => false,
				'safe' => true,
				'name' => tra('CSS Style'),
				'description' => tra('Enter CSS styling tags for the div type used e.g. padding: 5px'),
				'since' => '13.0',
				'filter' => 'text',
				'default' => '',
			],
			'id' => [
				'required' => false,
				'safe' => true,
				'name' => tra('ID'),
				'description' => tra('ID'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			],

		],
	];
}

function wikiplugin_box($data, $params)
{
//	global $tikilib;

	// Remove first <ENTER> if exists...
	// if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);

	extract($params, EXTR_SKIP);
	$bg   = (isset($bg)) ? " background: $bg;" : "";
	$align = (isset($align)) ? " text-align: $align;" : "";
	$id = (isset($id)) ? " id=\"$id\" " : '';
	$class = (isset($class)) ? ' ' . $class : ' ';
	$w = (isset($width)) ? " width: $width;" : "";
	$f = (isset($float) && ($float == "left" || $float == "right")) ? " float:$float" : "";
	$c = (isset($clear)) ? " clear:both;" : "";
	$style = (isset($style)) ? "$style;" : "";
	if (empty($float)) {
		$begin = "<div class='panel panel-default$class' $id style='$bg margin:0; $w $c $style $align'>";
	} else {
		$begin = "<div class='panel panel-default$class' $id style='$bg $f; margin:1em; margin-$float:0; $w $c $style $align'>";
	}

	if (isset($title)) {
		$begin .= "<div class='panel-heading'>$title</div>";
	}
	$begin .= "<div class='panel-body'" . (strlen($bg) > 0 ? " style=\"$bg\"" : "") . ">";
	$end = "</div></div>";
	// Prepend any newline char with br
	//$data = preg_replace("/\\n/", "<br />", $data);
	// Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
	//if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	//$data = TikiLib::lib('parser')->parse_data($data);
	$data = TikiLib::lib('parser')->protectSpecialChars($data); //they are unprotected before calling the plugins
	return $begin . $data . $end;
}
