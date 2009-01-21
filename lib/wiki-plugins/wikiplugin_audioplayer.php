<?php
function wikiplugin_audioplayer_info() {
	return array(
				 'name' => tra('Flash Mp3 Player'),
				 'documentation' => 'See http://flash-mp3-player.net/ http://code.google.com/p/mp3player for additional parameters',
				 'description' => 'Simple MP3 Player',
				 'params' => array(
								   'mp3' => array(
												  'required' => true,
												  'name'=> tra('MP3 URL'),
												  'description' => tra('Complete URL to the MP3 to include.'),
												  ),
								   'style' => array(
													'required' => false,
													'name' => tra('Style'),
													'description' => tra('One of:').'mini|normal|maxi|multi',
													),

								   ),
				 );
}
function wikiplugin_audioplayer($data, $params) {
	if (empty($params['mp3']))
		return;
	$defaults = array(
		'width' => 200,
		'height' => 20,
		'player' => 'player_mp3.swf',
		'where' => 'http://flash-mp3-player.net/medias/',
	);
	$params = array_merge( $defaults, $params );
	$styles = array('normal', 'mini', 'maxi', 'multi');
	if (empty($params['style']) || $params['style'] == 'normal' || !in_array($params['style'], $styles)) {
		$player = $params['player'];
	} else {
		$player = str_replace('.swf', '_'.$params['style'].'.swf', $params['player']);
	}
	echo $player;
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