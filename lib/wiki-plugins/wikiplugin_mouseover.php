<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_mouseover_info()
{
	global $prefs;
	include_once('lib/prefs/jquery.php');
	$jqprefs = prefs_jquery_list();
	$jqfx = [];
	foreach ($jqprefs['jquery_effect']['options'] as $k => $v) {
		$jqfx[] = ['text' => $v, 'value' => $k];
	}


	return [
		'name' => tra('Mouseover'),
		'documentation' => 'PluginMouseover',
		'description' => tra('Display hidden content by mousing over text'),
		'prefs' => [ 'wikiplugin_mouseover' ],
		'body' => tra('Hidden content, unless the label parameter is undefined, in which case this is the label.'),
		'iconname' => 'comment',
		'introduced' => 3,
		'tags' => [ 'basic' ],
		'params' => [
			'label' => [
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Text displayed on the page. The body is the hidden content.'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			],
			'url' => [
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Destination link when mouseover text is clicked. Use http:// for external links'),
				'since' => '3.0',
				'filter' => 'url',
				'default' => 'javascript:void(0)',
			],
			'text' => [
				'required' => false,
				'name' => tra('Text'),
				'description' => tra('DEPRECATED') . ' ' . tra('Hidden content. The body contains the label.'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
				'advanced' => true,
			],
			'width' => [
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Mouseover box width. Default: %0400px%1', '<code>', '</code>'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => 400,
				'advanced' => true,
			],
			'height' => [
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Mouseover box height. Default: %0200px%1', '<code>', '</code>'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => 200,
				'advanced' => true,
			],
			'offsetx' => [
				'required' => false,
				'name' => tra('Offset X'),
				'description' => tr('Shifts the overlay to the right by the specified number of pixels relative to
					the cursor. Default: %05%1', '<code>', '</code>'),
				'since' => '3.0',
				'filter' => 'int',
				'default' => 5,
				'advanced' => true,
			],
			'offsety' => [
				'required' => false,
				'name' => tra('Offset Y'),
				'description' => tr('Shifts the overlay lower by the specified number of pixels relative to the
					cursor. Default: %00%1', '<code>', '</code>'),
				'since' => '3.0',
				'filter' => 'int',
				'default' => 24,
				'advanced' => true,
			],
			'parse' => [
				'required' => false,
				'name' => tra('Parse Body'),
				'description' => tra('Parse the body of the plugin as wiki content (parsed by default)'),
				'since' => '3.0',
				'filter' => 'alpha',
				'advanced' => true,
				'default' => 'y',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				]
			],
			'parselabel' => [
				'required' => false,
				'name' => tra('Parse Label'),
				'description' => tra('Parse the label as wiki content (parsed by default)'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'y',
				'advanced' => true,
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				]
			],
			'class' => [
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('CSS class to apply'),
				'since' => '4.0',
				'filter' => 'text',
				'default' => 'plugin-mouseover',
				'advanced' => true,
			],
			'bgcolor' => [
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('Background color to apply to the popup'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
				'advanced' => true,
			],
			'textcolor' => [
				'required' => false,
				'name' => tra('Text Color'),
				'description' => tra('Color to apply to the text in the popup'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
				'advanced' => true,
			],
			'sticky' => [
				'required' => false,
				'name' => tra('Sticky'),
				'description' => tra('When enabled, popup stays visible until it is clicked.'),
				'since' => '3.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
				'advanced' => true,
			],
			'padding' => [
				'required' => false,
				'name' => tra('Padding'),
				'description' => tra('Padding size in pixels'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => '',
				'advanced' => true,
			],
			'effect' => [
				'required' => false,
				'name' => tra('Effect'),
				'options' => $jqfx,
				'description' => tra('Set the type of show/hide animation that will be used'),
				'since' => '4.0',
				'filter' => 'text',
				'advanced' => true,
			],
			'speed' => [
				'required' => false,
				'name' => tra('Effect Speed'),
				'options' => [
					['text' => tra('Normal'), 'value' => ''],
					['text' => tra('Fast'), 'value' => 'fast'],
					['text' => tra('Slow'), 'value' => 'slow'],
				],
				'description' => tra('Set the speed of the animation.'),
				'since' => '4.0',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
			],
			'closeDelay' => [
				'required' => false,
				'name' => tra('Close Delay'),
				'description' => tra('Number of seconds before popup closes'),
				'since' => '5.0',
				'filter' => 'digits',
				'default' => 0,
				'advanced' => true,
			],
			'tag' => [
				'required' => false,
				'name' => tra('Tag'),
				'description' => tr('HTML tag to use for the label. Default %0a%1', '<code>', '</code>'),
				'since' => '9.2',
				'filter' => 'word',
				'default' => 'a',
				'advanced' => true,
			],
		],
	];
}

function wikiplugin_mouseover($data, $params)
{
	$default = ['parse' => 'y', 'parselabel' => 'y'];
	$params = array_merge($default, $params);

	$width = isset($params['width']) ? (int) $params['width'] : 400;
	$height = isset($params['height']) ? (int) $params['height'] : 200;
	$offsetx = isset($params['offsetx']) ? (int) $params['offsetx'] : 5;
	$offsety = isset($params['offsety']) ? (int) $params['offsety'] : 24;
	$parse = ! isset($params['parse']) || (strcasecmp($params['parse'], 'n') != 0);
	$sticky = isset($params['sticky']) && $params['sticky'] == 'y';
	$padding = isset($params['padding']) ? 'padding: ' . $params['padding'] . 'px;' : '';
	$effect = ! isset($params['effect']) || $params['effect'] == 'Default' ? '' : strtolower($params['effect']);
	$speed = ! isset($params['speed']) ? 'normal' : strtolower($params['speed']);
	$closeDelay = isset($params['closeDelay']) ? (int) $params['closeDelay'] : 0;
	$tag = ! empty($params['tag']) ? $params['tag'] : 'a';

	if (empty($params['label']) && empty($params['text'])) {
		$label = tra('No label specified');
	} else {
		$label = ! empty($params['label']) ? $params['label'] : $data;
		$text = ! empty($params['text']) ? $params['text'] : $data;
	}

	$url = '';
	if (isset($params['url'])) {
		$url = $params['url'];
		$url = htmlentities($url, ENT_QUOTES, 'UTF-8');
	}

	$text = trim($text);

	if (empty($text)) {
		if ($params['parselabel'] == 'y') {
			return $label;
		} else {
			return "~np~$label~/np~";
		}
	}

	if ($parse) {
		$options = ['is_html' => 0];
		if (containsStringHTML($text)) {
			$options = ['is_html' => 1];
		}
		$text = TikiLib::lib('parser')->parse_data($text, $options);
	}
	if ($params['parselabel'] == 'y') {
		$label = "~/np~$label~np~";
	}

	static $lastval = 0;
	$id = "mo" . ++$lastval;

	$headerlib = TikiLib::lib('header');
	$headerlib->add_css('.plugin-mouseover-anchor:not([href]) { border-bottom: 1px dotted; color: inherit; cursor: help; text-decoration: none; }');

	if ($closeDelay && $sticky) {
		$closeDelayStr = "setTimeout(function() {hideJQ('#$id', '$effect', '$speed')}, " . ($closeDelay * 1000) . ");";
	} else {
		$closeDelayStr = '';
	}

	$js = "\$('#$id-link').mouseover(function(event) {
	var pos  = $(this).position();
	\$('#$id').css('position', 'absolute').css('left', pos.left + $offsetx + 'px').css('top', pos.top + $offsety + 'px');
	showJQ('#$id', '$effect', '$speed'); $closeDelayStr });
";
	if ($sticky) {
		$js .= "\$('#$id').click(function(event) { hideJQ('#$id', '$effect', '$speed'); }).css('cursor','pointer');\n";
	} else {
		$js .= "\$('#$id-link').mouseout(function(event) { setTimeout(function() {hideJQ('#$id', '$effect', '$speed')}, " . ($closeDelay * 1000) . "); });";
	}
	$headerlib->add_jq_onready($js);

	$bgcolor   = isset($params['bgcolor']) ? ("background-color: " . $params['bgcolor'] . ';') : '';
	$textcolor = isset($params['textcolor']) ? ("color:" . $params['textcolor'] . ';') : '';
	$class     = ! isset($params['class']) ? 'class="plugin-mouseover"' : 'class="plugin-mouseover ' . $params['class'] . '"';
	$href      = $url ? 'href="' . $url . '"' : '';

	$html = "~np~<$tag id=\"$id-link\" $href class=\"plugin-mouseover-anchor\">$label</$tag>" .
		"<span id=\"$id\" $class style=\"width: {$width}px; " . (isset($params['height']) ? "height: {$height}px; " : "") . "{$bgcolor} {$textcolor} {$padding} \">$text</span>~/np~";

	return $html;
}

function containsStringHTML($str)
{
	return preg_match('/<[^>]*>/', $str) == 1;
}
