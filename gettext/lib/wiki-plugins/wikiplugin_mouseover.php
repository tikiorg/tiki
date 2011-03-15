<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Plugin mouseover
 */
function wikiplugin_mouseover_help() {
	return tra("Create a mouseover feature on some text").":<br />~np~{MOUSEOVER(url=url,text=text,parse=y,width=300,height=300)}".tra('text')."{MOUSEOVER}~/np~";
}

function wikiplugin_mouseover_info() {
	global $prefs;
	include_once('lib/prefs/jquery.php');
	$jqprefs = prefs_jquery_list();
	$jqjx = array();
	foreach($jqprefs['jquery_effect']['options'] as $k => $v) {
		$jqfx[] = array('text' => $v, 'value' => $k);
	}
	
	
	return array(
		'name' => tra('Mouseover'),
		'documentation' => 'PluginMouseover',
		'description' => tra('Display hidden content by mousing over a text'),
		'prefs' => array( 'wikiplugin_mouseover' ),
		'body' => tra('Hidden content, unless the label parameter is undefined, in which case this is the label.'),
		'icon' => 'pics/icons/comment_add.png',
		'params' => array(
			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Text displayed on the page. The body is the hidden content.'),
				'filter' => 'striptags',
				'default' => '',
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Destination link when mouseover text is clicked. Use http:// for external links'),
				'filter' => 'url',
				'default' => 'javascript:void(0)',
			),
			'text' => array(
				'required' => false,
				'name' => tra('Text'),
				'description' => tra('DEPRECATED').' '.tra('Hidden content. The body contains the label.'),
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true,
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Mouseover box width. Default: 400px'),
				'filter' => 'digits',
				'default' => 400,
				'advanced' => true,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Mouseover box height. Default: 200px'),
				'filter' => 'digits',
				'default' => 200,
				'advanced' => true,
			),
			'offsetx' => array(
				'required' => false,
				'name' => tra('Offset X'),
				'description' => tra('Shifts the overlay to the right by the specified number of pixels relative to the cursor. Default: 5'),
				'filter' => 'digits',
				'default' => 5,
				'advanced' => true,
			),
			'offsety' => array(
				'required' => false,
				'name' => tra('Offset Y'),
				'description' => tra('Shifts the overlay lower by the specified number of pixels relative to the cursor. Default: 0'),
				'filter' => 'digits',
				'default' => 0,
				'advanced' => true,
			),
			'parse' => array(
				'required' => false,
				'name' => tra('Parse Body'),
				'description' => tra('Parse the body of the plugin as wiki content (parsed by default)'),
				'filter' => 'alpha',
				'advanced' => true,
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'parselabel' => array(
				'required' => false,
				'name' => tra('Parse Label'),
				'description' => tra('Parse the label as wiki content (parsed by default)'),
				'filter' => 'alpha',
				'default' => 'y',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('CSS class to apply'),
				'filter' => 'alpha',
				'default' => 'plugin-mouseover',
				'advanced' => true,
			),
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('Background color to apply to the popup'),
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true,
			),
			'textcolor' => array(
				'required' => false,
				'name' => tra('Text Color'),
				'description' => tra('Color to apply to the text in the popup'),
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true,
			),
			'sticky' => array(
				'required' => false,
				'name' => tra('Sticky'),
				'description' => tra('When enabled, popup stays visible until it is clicked.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),				
			'padding' => array(
				'required' => false,
				'name' => tra('Padding'),
				'description' => tra('Padding size in pixels'),
				'filter' => 'digits',
				'default' => '',
				'advanced' => true,
			),
			'effect' => array(
				'required' => false,
				'name' => tra('Effect'),
				'options' => $jqfx,
				'description' => tra('Set the type of show/hide animation that will be used'),
				'filter' => 'striptags',
				'advanced' => true,
			),
			'speed' => array(
				'required' => false,
				'name' => tra('Effect Speed'),
				'options' => array(
					array('text' => tra('Normal'), 'value' => ''), 
					array('text' => tra('Fast'), 'value' => 'fast'), 
					array('text' => tra('Slow'), 'value' => 'slow'), 
				),
				'description' => tra('Set the speed of the animation.'),
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
			),
			'closeDelay' => array(
				'required' => false,
				'name' => tra('Close Delay'),
				'description' => tra('Number of seconds before popup closes'),
				'filter' => 'digits',
				'default' => 0,
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_mouseover( $data, $params ) {
	global $smarty, $tikilib;

	$default = array('parse'=>'y', 'parselabel'=>'y');
	$params = array_merge($default, $params);
	if( ! isset($params['url']) ) {
		$url = 'javascript:void(0)';
	} else {
		$url = $params['url'];
	}

	$width = isset( $params['width'] ) ? (int) $params['width'] : 400;
	$height = isset( $params['height'] ) ? (int) $params['height'] : 200;
	$offsetx = isset( $params['offsetx'] ) ? (int) $params['offsetx'] : 5;
	$offsety = isset( $params['offsety'] ) ? (int) $params['offsety'] : 0;
	$parse = ! isset($params['parse']) || (strcasecmp($params['parse'], 'n') != 0);
	$sticky = isset($params['sticky']) && $params['sticky'] == 'y';
	$padding = isset( $params['padding'] ) ? 'padding: '.$params['padding'].'px;' : '';
	$effect = !isset( $params['effect'] ) || $params['effect'] == 'Default' ? '' : strtolower($params['effect']);
	$speed = !isset( $params['speed'] ) ? 'normal' : strtolower($params['speed']);
	$closeDelay = isset( $params['closeDelay'] ) ? (int) $params['closeDelay'] : 0;
	
	if (empty($params['label']) && empty($params['text'])) {
		$label = tra('No label specified');
	} else {
		$label = !empty( $params['label'] ) ? $params['label'] : $data;
		$text = !empty( $params['text'] ) ? $params['text'] : $data;
	}

	$text = trim($text);
	
	if (empty($text)) {
		if ($params['parselabel'] == 'y') {
			return $label;
		} else {
			return "~np~$label~/np~";
		}
	}

	if( $parse ) {
		$text = $tikilib->parse_data($text);
	}
	if( $params['parselabel'] == 'y' ) {
		$label = "~/np~$label~np~";
	}

	static $lastval = 0;
	$id = "mo" . ++$lastval;

	$url = htmlentities( $url, ENT_QUOTES, 'UTF-8' );

	global $headerlib;
	
	if ($closeDelay && $sticky) {
		$closeDelayStr = "setTimeout(function() {hideJQ('#$id', '$effect', '$speed')}, ".($closeDelay * 1000).");";
	} else {
		$closeDelayStr = '';
	}

	$js = "\$('#$id-link').mouseover(function(event) {
	\$('#$id').css('left', event.pageX + $offsetx).css('top', event.pageY + $offsety); showJQ('#$id', '$effect', '$speed'); $closeDelayStr });";
	if ($sticky) {
		$js .= "\$('#$id').click(function(event) { hideJQ('#$id', '$effect', '$speed'); }).css('cursor','pointer');\n";
	} else {
		$js .= "\$('#$id-link').mouseout(function(event) { setTimeout(function() {hideJQ('#$id', '$effect', '$speed')}, ".($closeDelay * 1000)."); });";
	}
	$headerlib->add_jq_onready($js);
	
	$bgcolor   =  isset($params['bgcolor'])   ? ("background-color: " . $params['bgcolor'] . ';') : '';
	$textcolor =  isset($params['textcolor']) ? ("color:" . $params['textcolor'] . ';') : '';
	$class     = !isset( $params['class'] )   ? 'class="plugin-mouseover"' : 'class="'.$params['class'].'"';
	
	$html = "~np~<a id=\"$id-link\" href=\"$url\">$label</a>".
		"<span id=\"$id\" $class style=\"width: {$width}px; " . (isset($params['height']) ? "height: {$height}px; " : "") ."{$bgcolor} {$textcolor} {$padding} \">$text</span>~/np~";

	return $html;
}
