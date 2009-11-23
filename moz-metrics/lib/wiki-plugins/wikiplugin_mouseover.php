<?php
/*
 * $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mouseover/wiki-plugins/wikiplugin_mouseover.php,v 1.2 2008-03-17 17:59:19 sylvieg Exp $
 * PLugin mouseover - See documentation http://www.bosrup.com/web/overlib/?Documentation
 */
function wikiplugin_mouseover_help() {
	return tra("Create a mouseover feature on some text").":<br />~np~{MOUSEOVER(url=url,text=text,parse=y,width=300,height=300)}".tra('text')."{MOUSEOVER}~/np~";
}

function wikiplugin_mouseover_info() {
	return array(
		'name' => tra('Mouseover'),
		'documentation' => 'PluginMouseover',
		'description' => tra('Create a mouseover feature on some text'),
		'prefs' => array( 'wikiplugin_mouseover' ),
		'body' => tra('Mouseover text if param label exists. Page text if text param exists'),
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
				'description' => 'Options: None|Default|Slide|Fade (and with jQuery UI enabled: Blind|Clip|Drop|Explode|Fold|Puff|Slide)',
				'filter' => 'alpha',
			),
			'speed' => array(
				'required' => false,
				'name' => tra('Effect speed'),
				'description' => 'Options: Fast|Normal|Slow',
				'filter' => 'alpha',
			),
		),
	);
}

function wikiplugin_mouseover( $data, $params ) {
	global $smarty, $tikilib;

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
	
	if (empty($params['label']) && empty($params['text'])) {
		$label = tra('No label specified');
	} else {
		$label = !empty( $params['label'] ) ? $params['label'] : $data;
		$text = !empty( $params['text'] ) ? $params['text'] : $data;
	}

	$text = trim($text);

	if( $parse ) {
		// Default output of the plugin is in ~np~, so escape it if content has to be parsed.
		$text = "~/np~$text~np~";
	}

	static $lastval = 0;
	$id = "mo" . ++$lastval;

	$url = htmlentities( $url, ENT_QUOTES, 'UTF-8' );

	global $headerlib;

	$js = "\$jq('#$id-link').mouseover(function(event) {
	\$jq('#$id').css('left', event.pageX + $offsetx).css('top', event.pageY + $offsety); showJQ('#$id', '$effect', '$speed'); });";
	if ($sticky) {
		$js .= "\$jq('#$id').click(function(event) { hideJQ('#$id', '$effect', '$speed'); }).css('cursor','pointer');\n";
	} else {
		$js .= "\$jq('#$id-link').mouseout(function(event) { setTimeout(function() {hideJQ('#$id', '$effect', '$speed')}, 250); });";
	}
	$headerlib->add_jq_onready($js);
	
	$bgcolor   =  isset($params['bgcolor'])   ? ("background-color: " . $params['bgcolor'] . ';') : '';
	$textcolor =  isset($params['textcolor']) ? ("color:" . $params['textcolor'] . ';') : '';
	$class     = !isset( $params['class'] )   ? 'class="plugin-mouseover"' : 'class="'.$params['class'].'"';
	
	$html = "~np~<a id=\"$id-link\" href=\"$url\">$label</a>".
		"<span id=\"$id\" $class style=\"width: {$width}px; " . (isset($params['height']) ? "height: {$height}px; " : "") ."{$bgcolor} {$textcolor} {$padding} \">$text</span>~/np~";

	return $html;
}
