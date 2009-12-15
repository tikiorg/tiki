<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_flash.php,v 1.8.2.1 2007-11-29 00:25:57 xavidp Exp $
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
								   'mp3' => array(
												  'required' => false,
												  'name'=> tra('MP3 URL'),
												  'description' => tra('Complete URL to the mp3 to include.'),
												  ),
								   'flv' => array(
												  'required' => false,
												  'name'=> tra('FLV URL'),
												  'description' => tra('Complete URL to the flv to include.'),
												  ),
								   'style' => array(
													'required' => false,
													'name' => tra('Style'),
													'description' => tra('One of:').'mini|normal|maxi|multi',
													),

								   ),
				 );
}
function wikiplugin_mediaplayer($data, $params) {
	if (empty($params['mp3']) && empty($params['flv'])) {
		return;
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
	if (!empty($params['flv'])) {
		$params = array_merge($defaults_flv, $params );
	} else {
		$params = array_merge($defaults_mp3, $params );
	}
	$styles = array('normal', 'mini', 'maxi', 'multi');
	if (empty($params['style']) || $params['style'] == 'normal' || !in_array($params['style'], $styles)) {
		$player = $params['player'];
	} else {
		$player = str_replace('.swf', '_'.$params['style'].'.swf', $params['player']);
	}
	$code = '<object type="application/x-shockwave-flash" data="'.$params['where'].$player.'" width="'.$params['width'].'" height="'.$params['height'].'">';
	$code .= '<param name="movie" value="'.$params['where'].$player.'" />';
	if (empty($params['flv']) && !empty($params['mp3'])) 
		$code .= '<param name="FlashVars" value="mp3='.$params['mp3'].'" />';
	unset($params['width']); unset($params['height']); unset($params['where']); unset($params['player']);unset($params['mp3']); unset($params['style']);
	foreach ($params as $key=>$value) {
		$code .= '<param name="FlashVars" value="'.$key.'='.$value.'" />';
	}
	$code .= '</object>';
	
	return "~np~$code~/np~";
}