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
		'body' => tra('Mouseover text is param label exists. Page text is text param exists'),
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
				'description' => tra('Text displayed on the page. The body is the mouseover content'),
				'filter' => 'striptags',
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
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Color of the inside popup'),
				'description' => tra('#000000'),
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
	$sticky = isset($params['sticky']) && $params['sticky'] == 'y';

	$text = isset( $params['text'] ) ? $params['text'] : tra('No label specified');

	$data = trim($data);

	if( $parse ) {
		// Default output of the plugin is in ~np~, so escape it if content has to be parsed.
		$data = "~/np~$data~np~";
	}

	static $lastval = 0;
	$id = "mo" . ++$lastval;

	$url = htmlentities( $url, ENT_QUOTES, 'UTF-8' );

	if ( !empty($param['center']) || !empty($param['left']) || !empty($param['right']) || !empty($param['above']) || !empty($param['bellow'])) {
		if (!$smarty->get_template_vars('overlib_loaded')) {
			$html .= '<div id="'.$id.'" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
			$html .= '<script type="text/javascript" src="lib/overlib.js"></script>';
			$smarty->assign('overlib_loaded',1);
		}
		$data = preg_replace('/\r\n/', '<br />', $data);
		$html .= "<a href='$url'";
		$html .= " onmouseover=\"return overlib('".str_replace("'", "\'", htmlspecialchars($data))."'";
		foreach ($params as $param=>$value) {
			$p = strtoupper($param);
			if ($p != 'URL' && $p != 'TEXT' && $p != 'PARSE' && $p != 'LABEL') {
				if ((!empty($value) || $value != 'n') && ($p == 'STICKY' || $p == 'LEFT' || $p == 'RIGHT' || $p == 'CENTER' || $p == 'ABOVE'  || $p == 'BELOW'  || $p == 'AUTOSTATUS' || $p == 'AUTOSTATUSCAP' || $p == 'HAUTO' || $p == 'VAUTO' || $p == 'CLOSECLICK' || $p == 'FULLHTML' || $p == 'CSSOFF' || $p == 'CSSSTYLE' || $p == 'CSSCLASS' || $p == 'NOCLOSE')) {
					$html .= ','.$p;
				} else {
					$html .= ','.$p;
					$html .= ",'$value'";	
				}
			}
		}
		$html .= ");\" onmouseout='nd();' >";
		$html .= "$text</a>";
	} else {
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
		$bgcolor = "background-color: " . ( isset($params['bgcolor']) ? $params['bgcolor'] : 'white' ) . ';';
		$textcolor = isset($params['textcolor']) ? ("color:" . $params['textcolor'] . ';') : '';

		$html = "~np~<a id=\"$id-link\" href=\"$url\">$text</a><div id=\"$id\" style=\"width: {$width}px; height: {$height}px; {$bgcolor} {$textcolor} display:none; position: absolute; z-index: 500;\">$data</div>~/np~";
	}
	return $html;
}
?>
