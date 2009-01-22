<?php
function wikiplugin_mp3_flv_player_info() {
	return array(
				 'name' => tra('Flash Mp3 Player'),
				 'documentation' => 'See http://flash-mp3-player.net/ http://code.google.com/p/mp3player for additional parameters',
				 'description' => 'Simple MP3 Player',
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
function wikiplugin_mp3_flv_player($data, $params) {
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
	$code .= '<param name="FlashVars" value="mp3='.$params['mp3'].'" />';
	unset($params['width']); unset($params['height']); unset($params['where']); unset($params['player']);unset($params['mp3']); unset($params['style']);
	foreach ($params as $key=>$value) {
		$code .= '<param name="FlashVars" value="'.$key.'='.$value.'" />';
	}
	$code .= '</object>';
	
	return "~np~$code~/np~";
}