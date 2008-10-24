<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_flash.php,v 1.8.2.1 2007-11-29 00:25:57 xavidp Exp $

// Wiki plugin to display a SWF file
// damian aka damosoft 30 March 2004

function wikiplugin_flash_help() {
        return tra("Displays a SWF on the wiki page").":<br />~np~{FLASH(movie=\"url_to_flash\",width=>xx,height=>xx,quality=>high)}{FLASH}~/np~";
}

function wikiplugin_flash_info() {
	return array(
		'name' => tra('Flash video'),
		'documentation' => 'PluginFlash',
		'description' => tra('Displays a SWF on the wiki page'),
		'prefs' => array('wikiplugin_flash'),
		'params' => array(
			'movie' => array(
				'required' => true,
				'name' => tra('Movie URL'),
				'description' => tra('Complete URL to the movie to include.'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Default width: 425'),
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Default height: 350'),
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tra('Flash video quality. Default value: high'),
			),
		),
	);
}

function wikiplugin_flash($data, $params) {
	static $id = 0;
	
	global $headerlib;

	if (! isset($params['movie']) ) {
		return tra('Missing parameter movie to the plugin flash');
	}
	
	$defaults = array(
		'width' => 425,
		'height' => 350,
		'quality' => 'high',
		'version' => '9.0.0',
	);

	$myId = 'wp-flash-' . ++$id;
	$params = array_merge( $defaults, $params );

	$movie = json_encode( $params['movie'] );
	$div = json_encode( $myId );
	$width = (int) $params['width'];
	$height = (int) $params['height'];
	$version = json_encode( $params['version'] );

	unset( $params['movie'], $params['width'], $params['height'], $params['version'] );
	$params = json_encode($params);

	$js = <<<JS
swfobject.embedSWF( $movie, $div, $width, $height, $version, {}, $params, {} );
JS;

	$headerlib->add_jsfile( 'lib/swfobject.js' );
	$headerlib->add_js( $js );

	return "~np~<div id=\"$myId\">" . tra('Flash player not available.') . "</div>~/np~";
}

?>
