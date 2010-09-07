<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_mediaplayer_help() {
	return tra('Inline Flash mp3 and flv Player.')."<br />~np~{MEDIAPLAYER(mp3=\"url_to_mp3\", flv=\"url_to_flv\",style=normal) /}"; 
}
function wikiplugin_mediaplayer_info() {
	return array(
		'name' => tra('Mediaplayer'),
		'documentation' => 'PluginMediaplayer',
		'description' => 'Simple mp3 or flv Player',
		'extraparams' =>true,
		'prefs' => array( 'wikiplugin_mediaplayer' ),
		'params' => array(
			'fullscreen' => array(
				'required' => false,
				'name' => tra('Allow Fullscreen'),
				'description' => tra('Allow fullscreen mode.').' true|false',
				'filter' => 'alpha',
				'options' => array(
					array(
						'text' => tra('Yes'),
						'value' => 'true'
					),
					array(
						'text' => tra('No'),
						'value' => 'false'
					)
				)
			),
			'mp3' => array(
				'required' => false,
				'name'=> tra('MP3 URL'),
				'description' => tra('Complete URL to the mp3 to include.'),
				'filter' => 'url'
			),
			'flv' => array(
				'required' => false,
				'name'=> tra('FLV URL'),
				'description' => tra('Complete URL to the flv to include.'),
				'filter' => 'url'
			),
			'src' => array(
				'required' => false,
				'name'=> tra('URL'),
				'description' => tra('Complete URL to the media to include.'). ' asx, asf, avi, flv, mov, mpg, mpeg, mp4, qt, ra, smil, swf, wmv, 3g2, 3gp,aif, aac, au, gsm, mid, midi, mov, mp3, m4a, snd, ra, ram, rm, wav, wma, bmp, html, pdf, psd, qif, qtif, qti, tif, tiff, xaml',
				'filter' => 'url'
			),
			'style' => array(
				'required' => false,
				'name' => tra('Style'),
				'description' => tra('One of:').'mini|normal|maxi|multi',
				'filter' => 'alpha',
				'options' => array(
					array(
						'text' => 'mini', 'value' => 'mini'
					),
					array(
						'text' => 'normal', 'value' => 'normal'
					),
					array(
						'text' => 'maxi', 'value' => 'maxi'
					),
					array(
						'text' => 'multi', 'value' => 'multi'
					)
				)
			),
			'wmode' => array(
				'required' => false,
				'name' => tra('Flash Window Mode'),
				'description' => tra('Sets the Window Mode property of the Flash movie for transparency, layering, and positioning in the browser. Default value: ').'transparent',
				'filter' => 'alpha',
				'options' => array(
					array(
						'text' => 'transparent'.tra(' - show background through and allow to be covered'),
						'value' => 'transparent'
					),
					array(
						'text' => 'opaque'.tra(' - hide everything behind'),
						'value' => 'opaque'
					),
					array(
						'text' => 'window'.tra(' - play movie in its own rectangular window on a web page'),
						'value' => 'window'
					)
				)
			),
		),
	);
}
function wikiplugin_mediaplayer($data, $params) {
	global $prefs, $access;
	static $iMEDIAPLAYER = 0;
	$id = 'mediaplayer'.++$iMEDIAPLAYER;

	if (empty($params['mp3']) && empty($params['flv']) && empty($params['src'])) {
		return;
	}
	if (!empty($params['src'])) {
		$access->check_feature('feature_jquery_media');
	}
	$defaults_mp3 = array(
		'width' => 200,
		'height' => 20,
		'player' => 'player_mp3.swf',
		'where' => 'http://flash-mp3-player.net/medias/',
	);
	$defaults_flv = array(
		'width' => 320,
		'height' => 240,
		'player' => 'player_flv.swf',
		'where' => 'http://flv-player.net/medias/',
	);
	$defaults = array(
		'width' => 320,
		'height' => 240,
	);
	if (!empty($params['flv'])) {
		$params = array_merge($defaults_flv, $params );
	} elseif (!empty($params['mp3'])) {
		$params = array_merge($defaults_mp3, $params );
	} else {
		$params = array_merge($defaults, $params );
	}
	if (!empty($params['src'])) {
		global $headerlib; include_once('lib/headerlib.php');
		$js = "\n var media_$id = $('#$id').media( {";
		foreach ($params as $param => $value) {
			if ($param == 'src') {
				continue;
			}
			
			if (is_numeric($value) == false &&
				strtolower($value) != 'true' && 
				strtolower($value) != 'false') {
				$value = "\"" . $value . "\"";
			}
			
			$js .= "$param: $value,";
		}
		$js .= "} );";
		$headerlib->add_jq_onready($js);
		return "<a href=\"".$params['src']."\" id=\"$id\"></a>";
	}
	$styles = array('normal', 'mini', 'maxi', 'multi');
	if (empty($params['style']) || $params['style'] == 'normal' || !in_array($params['style'], $styles)) {
		$player = $params['player'];
	} else {
		$player = str_replace('.swf', '_'.$params['style'].'.swf', $params['player']);
	}
	$code = '<object type="application/x-shockwave-flash" data="'.$params['where'].$player.'" width="'.$params['width'].'" height="'.$params['height'].'">';
	$code .= '<param name="movie" value="'.$params['where'].$player.'" />';
	if (!empty($params['fullscreen'])) {
		$code .= '<param name="allowFullscreen" value="'.$params['fullscreen'].'" />';
	}
	if (empty($params['wmode'])) {
		$wmode = 'transparent';
	} else {
		$wmode = $params['wmode'];
	}
	$code .= '<param name="wmode" value="'.$wmode.'" />';
	$code .= '<param name="FlashVars" value="';
	if (empty($params['flv']) && !empty($params['mp3'])) 
		$code .= 'mp3='.$params['mp3'];
	
	unset($params['width']); unset($params['height']); unset($params['where']); unset($params['player']);unset($params['mp3']); unset($params['style']); unset($params['fullscreen']); unset($params['wmode']);
	
	foreach ($params as $key=>$value) {
		$code .= '&amp;'.$key.'='.$value;
	}
	$code .= '" />';
	$code .= '</object>';

	return "~np~$code~/np~";
}
