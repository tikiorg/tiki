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
		'description' => tra('Create a mouseover feature on some text'),
		'prefs' => array( 'wikiplugin_mouseover' ),
		'body' => tra('Text displayed in the mouse over box.'),
		'params' => array(
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('?'),
				'filter' => 'url',
			),
			'text' => array(
				'required' => true,
				'name' => tra('Text'),
				'description' => tra('Text displayed on the page.'),
				'filter' => 'striptags',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Mouse over box width.'),
				'filter' => 'digits',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Mouse over box height.'),
				'filter' => 'digits',
			),
			'offsetx' => array(
				'required' => false,
				'name' => tra('Offset X'),
				'description' => tra('Shifts the overlay to the right by the specified amount of pixels in relation to the cursor.'),
				'filter' => 'digits',
			),
			'offsety' => array(
				'required' => false,
				'name' => tra('Offset Y'),
				'description' => tra('Shifts the overlay to the bottom by the specified amount of pixels in relation to the cursor.'),
				'filter' => 'digits',
			),
			'parse' => array(
				'required' => false,
				'name' => tra('Parse Body'),
				'description' => tra('y|n, parse the body of the plugin as wiki content. (Default to y)'),
				'filter' => 'alpha',
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

	$width = isset( $params['width'] ) ? (int) $params['width'] : 300;
	$height = isset( $params['height'] ) ? (int) $params['height'] : 300;
	$offsetx = isset( $params['offsetx'] ) ? (int) $params['offsetx'] : 0;
	$offsety = isset( $params['offsety'] ) ? (int) $params['offsety'] : 0;
	$parse = ! isset($params['parse']) || $params['parse'] != 'n';

	$text = isset( $params['text'] ) ? $params['text'] : 'No label specified';

	$data = trim($data);

	if( $parse ) {
		// Default output of the plugin is in ~np~, so escape it if content has to be parsed.
		$data = "~/np~$data~np~";
	}

	static $lastval = 0;
	$id = "mo" . ++$lastval;

	$url = htmlentities( $url, ENT_QUOTES, 'UTF-8' );

	global $headerlib;

	$headerlib->add_js( "
window.addEvent('domready', function() {
	$('$id-link').addEvent( 'mouseover', function(event) {
		$('$id').setStyle('left', (event.page.x + $offsetx) + 'px');
		$('$id').setStyle('top', (event.page.y + $offsety) + 'px');
		$('$id').setStyle('display','block');
	} );
	$('$id-link').addEvent( 'mouseout', function(event) {
		$('$id').setStyle('display','none');
	} );
} );
" );

	return "~np~<a id=\"$id-link\" href=\"$url\">$text</a><div id=\"$id\" style=\"width: {$width}px; height: {$height}px; display:none; position: absolute; z-index: 500; background: white;\">$data</div>~/np~";
}
?>
