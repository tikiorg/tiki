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
		'name' => tra('Mouse Over'),
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
				'description' => tra('Destination link when moused-over text is clicked. Use http:// for external links'),
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
				'description' => tra('Mouse over box width. Default: 400px'),
				'filter' => 'digits',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Mouse over box height. Default: 200px'),
				'filter' => 'digits',
			),
			'offsetx' => array(
				'required' => false,
				'name' => tra('Offset X'),
				'description' => tra('Shifts the overlay to the right by the specified amount of pixels in relation to the cursor. Default: 5px'),
				'filter' => 'digits',
			),
			'offsety' => array(
				'required' => false,
				'name' => tra('Offset Y'),
				'description' => tra('Shifts the overlay to the bottom by the specified amount of pixels in relation to the cursor. Default: 0px'),
				'filter' => 'digits',
			),
			'parse' => array(
				'required' => false,
				'name' => tra('Parse Body'),
				'description' => tra('y|n, parse the body of the plugin as wiki content. (Default to y)'),
				'filter' => 'alpha',
			),
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Color of the inside popup'),
				'description' => tra('Default: #F5F5F5'),
				'filter' => 'striptags',
			),
			'textcolor' => array(
				'required' => false,
				'name' => tra('Text popup color'),
				'description' => tra('#FFFFFF'),
				'filter' => 'striptags',
			),
			'sticky' => array(
				'required' => false,
				'name' => tra('Sticky'),
				'description' => 'y|n, when enabled, popup stays visible until an other one is displayed or it is clicked.',
				'filter' => 'alpha',
			),				
			'padding' => array(
				'required' => false,
				'name' => tra('Padding'),
				'description' => 'Default: 5px',
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_mouseover( $data, $params ) {
	global $smarty, $tikilib;

	if( ! isset($params['url']) ) {
		$url = 'javascript:void()';
	} else {
		$url = $params['url'];
	}

	$width = isset( $params['width'] ) ? (int) $params['width'] : 400;
	$height = isset( $params['height'] ) ? (int) $params['height'] : 200;
	$offsetx = isset( $params['offsetx'] ) ? (int) $params['offsetx'] : 5;
	$offsety = isset( $params['offsety'] ) ? (int) $params['offsety'] : 0;
	$parse = ! isset($params['parse']) || $params['parse'] != 'n';
	$sticky = isset($params['sticky']) && $params['sticky'] == 'y';
	$padding = isset( $params['padding'] ) ? (int) $params['padding'] : 5;
	
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

	$headerlib->add_js( "
window.addEvent('domready', function() {
	$('$id-link').addEvent( 'mouseover', function(event) {
		if( window.wikiplugin_mouseover )
			window.wikiplugin_mouseover.setStyle('display', 'none');

		window.wikiplugin_mouseover = $('$id');
		window.wikiplugin_mouseover.setStyle('left', (event.page.x + $offsetx) + 'px');
		window.wikiplugin_mouseover.setStyle('top', (event.page.y + $offsety) + 'px');
		window.wikiplugin_mouseover.setStyle('display','block');

		window.wikiplugin_mouseover.addEvent( 'click', function(event) {
			window.wikiplugin_mouseover.setStyle( 'display', 'none' );
		} );
	} );
	" . ( $sticky ? '' : "
	$('$id-link').addEvent( 'mouseout', function(event) {
		$('$id').setStyle('display','none');
	} ); " ) . "
} );
" );
	$bgcolor = "background-color: " . ( isset($params['bgcolor']) ? $params['bgcolor'] : '#F5F5F5' ) . ';';
	$textcolor = isset($params['textcolor']) ? ("color:" . $params['textcolor'] . ';') : '';

	$html = "~np~<a id=\"$id-link\" href=\"$url\">$label</a><div id=\"$id\" style=\"width: {$width}px; height: {$height}px; {$bgcolor} {$textcolor} display:none; padding: {$padding}px ;position: absolute; z-index: 500;\">$text</div>~/np~";

	return $html;
}
?>
