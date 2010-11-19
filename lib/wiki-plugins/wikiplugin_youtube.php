<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_youtube_help() {
        return tra("Display youtube video in a wiki page").":<br />~np~{YOUTUBE(movie=>\"url_to_youtube_video\")}{YOUTUBE}~/np~";
}

function wikiplugin_youtube_info() {
	return array(
		'name' => tra('Youtube'),
		'documentation' => 'PluginYouTube',
		'description' => tra('Display a YouTube video in a wiki page'),
		'prefs' => array( 'wikiplugin_youtube' ),
		'icon' => 'pics/icons/youtube.png',
		'params' => array(
			'movie' => array(
				'required' => true,
				'name' => 'Movie',
				'description' => tra('Entire URL to the YouTube video or last part (after www.youtube.com/v/)'),
				'filter' => 'url',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels'),
				'filter' => 'digits',
				'default' => 425,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels'),
				'filter' => 'digits',
			'default' => 350,
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tra('Quality of the video'),
				'default' => 'high',
				'filter' => 'alpha',
    			'options' => array(
					array('text' => tra('High'), 'value' => 'high'), 
					array('text' => tra('Medium'), 'value' => 'medium'), 
					array('text' => tra('Low'), 'value' => 'low'), 
				),  
				'advanced' => true				
			),
			'allowFullScreen' => array(
				'required' => false,
				'name' => tra('Full screen'),
				'description' => tra('Expand to full screen'),
				'default' => 'n',
				'filter' => 'alpha',
    			'options' => array(
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
				'advanced' => true				
			),
			'related' => array(
				'required' => false,
				'name' => tra('Related'),
				'description' => tra('Show related videos'),
				'introduced' => 7.0,
				'filter' => 'alpha',
    			'options' => array(
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
				'default' => 'n',
				'advanced' => true				
			),
			'background' => array(
				'required' => false,
				'name' => tra('Background'),
				'description' => tra('Toolbar background color'),
				'accepted' => tra('HTML color code, e.g. ffffff'),
				'introduced' => 7.0,
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true				
			),
			'border' => array(
				'required' => false,
				'name' => tra('Borders'),
				'description' => tra('Toolbar border colors'),
				'accepted' => tra('HTML color code, e.g. ffffff'),
				'introduced' => 7.0,
				'filter' => 'striptags',
				'default' => '',
				'advanced' => true				
			),
			),
	);
}

function wikiplugin_youtube($data, $params) {
	$plugininfo = wikiplugin_youtube_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	extract ($params,EXTR_SKIP);
	$params = array_merge($default, $params);

	if (empty($movie)) {
		return '^' . tra('Plugin YouTube error: the movie parameter is empty.');
	}

	$movie = "http://www.youtube.com/v/" . preg_replace('/http(s)?:\/\/(\w+\.)?youtube\.com\/watch\?v=/', '', $movie);
	if (!empty($allowFullScreen) && $allowFullScreen = 'y') {
		$movie .= '?fs=1';
		$fs = ' allowFullScreen="true" ';
	}
	if ($related == 'n') {
		$movie .= '&rel=0';
	}
	if (!empty($border)) {
		$movie .= '&color1=0x' . $border;
	}
	if (!empty($background)) {
		$movie .= '&color2=0x' . $background;
	}
	
	$asetup = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' . $width . '" height="' . $height . '">';
	$asetup .= "\n\t" . '<param name="movie" value="' . $movie . '"></param>';
	if (!empty($allowFullScreen) && $allowFullScreen = 'y') {
		$asetup .= "\n\t" . '<param name="allowFullScreen" value="true"></param>';
	}
	$asetup .= "\n\t" . '<param name="quality" value="' . $quality . '"></param>';
	$asetup .= "\n\t" . '<param name="wmode" value="transparent"></param>';
	$asetup .= "\n\t" . '<embed src="' . $movie . '" quality="' . $quality . '"'
					. "\n\t\t" . ' pluginspage="http://www.macromedia.com/go/getflashplayer"' 
					. "\n\t\t" . ' type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '"' .  $fs
					. "\n\t\t" . ' wmode="transparent">' . "\n\t" . '</embed>' . "\n" . '</object>' . "\n";
	return $asetup;
}
