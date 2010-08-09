<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * PLugin mouseover - See documentation http://www.bosrup.com/web/overlib/?Documentation
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
		'description' => tra('Create a mouseover feature on some text'),
		'prefs' => array( 'wikiplugin_mouseover' ),
		'body' => tra('Mouseover text if param label exists. Page text if text param exists'),
		'icon' => 'pics/icons/comment_add.png',
		'params' => array(
			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Text displayed on the page. The body is the mouseover content'),
				'filter' => 'striptags',
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Destination link when mouseover text is clicked. Use http:// for external links'),
				'filter' => 'url',
			),
			'text' => array(
				'required' => false,
				'name' => tra('Text'),
				'description' => tra('DEPRECATED').' '.tra('Text displayed on the mouseover. The body contains the text of the page.'),
				'filter' => 'striptags',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Mouseover box width. Default: 400px'),
				'filter' => 'digits',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Mouseover box height. Default: 200px'),
				'filter' => 'digits',
			),
			'offsetx' => array(
				'required' => false,
				'name' => tra('Offset X'),
				'description' => tra('Shifts the overlay to the right by the specified number of pixels relative to the cursor. Default: 5px'),
				'filter' => 'digits',
			),
			'offsety' => array(
				'required' => false,
				'name' => tra('Offset Y'),
				'description' => tra('Shifts the overlay lower by the specified number of pixels relative to the cursor. Default: 0px'),
				'filter' => 'digits',
			),
			'parse' => array(
				'required' => false,
				'name' => tra('Parse Body'),
				'description' => tra('y|n, parse the body of the plugin as wiki content. (Default to y)'),
				'filter' => 'alpha',
			),
			'parselabel' => array(
				'required' => false,
				'name' => tra('Parse Label'),
				'description' => 'y|n '.tra('parse label'),
				'filter' => 'alpha',
				'default' => 'y',
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => 'Default: plugin-mouseover',
				'filter' => 'alpha',
			),
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Background color of the popup'),
				'description' => tra(''),
				'filter' => 'striptags',
			),
			'textcolor' => array(
				'required' => false,
				'name' => tra('Text color in the popup'),
				'description' => tra(''),
				'filter' => 'striptags',
			),
			'sticky' => array(
				'required' => false,
				'name' => tra('Sticky'),
				'description' => 'y|n, when enabled, popup stays visible until it is clicked.',
				'filter' => 'alpha',
			),				
			'padding' => array(
				'required' => false,
				'name' => tra('Padding'),
				'description' => 'Default: 5px',
				'filter' => 'digits',
			),
			'effect' => array(
				'required' => false,
				'name' => tra('Effect'),
				'options' => $jqfx,
				'description' => 'Show/hide animation',
				'filter' => 'striptags',
			),
			'speed' => array(
				'required' => false,
				'name' => tra('Effect speed'),
				'options' => array(
					array('text' => tra('Normal'), 'value' => ''), 
					array('text' => tra('Fast'), 'value' => 'fast'), 
					array('text' => tra('Slow'), 'value' => 'slow'), 
				),
				'description' => '',
				'filter' => 'alpha',
			),
			'closeDelay' => array(
				'required' => false,
				'name' => tra('Close delay'),
				'description' => 'Number of seconds before popup closes',
				'filter' => 'digits',
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
	$parse = ! isset($params['parse']) || $params['parse'] != 'n';
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
		// Default output of the plugin is in ~np~, so escape it if content has to be parsed.
		$text = "~/np~$text~np~";
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
