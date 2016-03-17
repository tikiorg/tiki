<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_youtube_info()
{
	return array(
		'name' => tra('YouTube'),
		'documentation' => 'PluginYouTube',
		'description' => tra('Embed a YouTube video in a page'),
		'prefs' => array( 'wikiplugin_youtube' ),
		'iconname' => 'youtube',
		'introduced' => 2,
		'tags' => array( 'basic' ),
		'params' => array(
			'movie' => array(
				'required' => true,
				'name' => tra('Movie'),
				'description' => tr('Complete URL to the YouTube video or last part (after %0www.youtube.com/v/%1 and
					before the first question mark)', '<code>', '</code>'),
				'since' => '2.0',
				'filter' => 'url',
				'default' => '',
			),
			'privacyEnhanced' => array(
				'required' => false,
				'name' => tra('Privacy-Enhanced'),
				'description' => tra('Enable privacy-enhanced mode'),
				'default' => '',
				'filter' => 'alpha',
    			'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels.') . ' ' . tra('Default')  . ' :<code>425</code>',
				'since' => '2.0',
				'filter' => 'digits',
				'default' => 425,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels') . ' ' . tra('Default')  . ' :<code>350</code>',
				'since' => '2.0',
				'filter' => 'digits',
				'default' => 350,
			),
			'start' => array(
				'required' => false,
				'name' => tra('Start time'),
				'description' => tra('Start time offset in seconds'),
				'filter' => 'digits',
				'default' => 0,
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tr('Quality of the video. Default is %0high%1.', '<code>', '</code>'),
				'since' => '2.0',
				'default' => 'high',
				'filter' => 'alpha',
    			'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('High'), 'value' => 'high'),
					array('text' => tra('Medium'), 'value' => 'medium'),
					array('text' => tra('Low'), 'value' => 'low'),
				),
				'advanced' => true
			),
			'allowFullScreen' => array(
				'required' => false,
				'name' => tra('Allow full-screen'),
				'description' => tra('Enlarge video to full screen size'),
				'since' => '5.0',
				'default' => '',
				'filter' => 'alpha',
     			'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
 				),
 				'advanced' => true
			),
			'related' => array(
				'required' => false,
				'name' => tra('Related'),
				'description' => tra('Show related videos (shown by default)'),
				'since' => '6.1',
				'default' => '',
				'filter' => 'alpha',
    			'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
				'advanced' => true
			),
			'background' => array(
				'required' => false,
				'name' => tra('Background'),
				'description' => tra('Toolbar background color. Use an HTML color code.') . ' ' . tra('Example')
					. ': <code>ffffff</code>',
				'accepted' => tra('HTML color code, e.g. ffffff'),
				'since' => '6.1',
				'filter' => 'text',
				'default' => '',
				'advanced' => true
			),
			'border' => array(
				'required' => false,
				'name' => tra('Borders'),
				'description' => tra('Toolbar border colors. Use an HTML color code.') . ' ' . tra('Example')
					. ': <code>ffffff</code>',
				'accepted' => tra('HTML color code, e.g. ffffff'),
				'since' => '6.1',
				'filter' => 'text',
				'default' => '',
				'advanced' => true
			),
		),
	);
}

function wikiplugin_youtube($data, $params)
{
	global $tikilib;

 	$plugininfo = wikiplugin_youtube_info();
 	foreach ($plugininfo['params'] as $key => $param) {
 		$default["$key"] = $param['default'];
 	}
	$params = array_merge($default, $params);

	if (empty($params['movie'])) {
		return '^' . tra('Plugin YouTube error: the movie parameter is empty.');
	}

	$scheme = $tikilib->httpScheme();

	$sYoutubeId  = getYoutubeId($params['movie']);
	if (empty($sYoutubeId)) {
		return '^' . tra('Invalid YouTube URL provided');
	}

	if ($params['privacyEnhanced'] == 'y') {
		$fqdn = 'www.youtube-nocookie.com';
	} else {
		$fqdn = 'www.youtube.com';
	}

	$params['movie'] = '//'.$fqdn.'/embed/' . $sYoutubeId . '?';
	// backward compatibility
	if ($params['allowFullScreen'] == 'y') {
		$params['allowFullScreen'] = 'true';
	} else if ($params['allowFullScreen'] == 'n') {
		$params['allowFullScreen'] = 'false';
	}

	if (!empty($params['allowFullScreen'])) {
		if ($params['allowFullScreen'] == 'true') {
			$params['movie'] .= '&fs=1';
		} else {
			$params['movie'] .= '&fs=0';
		}
	}
	if (!empty($params['start'])) {
		$params['movie'] .= '&start=' . $params['start'];
	}
	if (isset($params['related']) && $params['related'] == 'n') {
		$params['movie'] .= '&rel=0';
	}
	if (!empty($params['border'])) {
		$params['movie'] .= '&color1=0x' . $params['border'];
	}
	if (!empty($params['background'])) {
		$params['movie'] .= '&color2=0x' . $params['background'];
	}


	$iframe = ('<iframe src="'.$params['movie'].'" frameborder="0" width="'.$params['width'].'" height="'.$params['height'].'" allowfullscreen="'.$params['allowFullScreen'].'"></iframe>');

	return '~np~' . $iframe . '~/np~';
}

function getYoutubeId( $sYoutubeUrl)
{
	$aParsedUrl = parse_url($sYoutubeUrl);
	if ($aParsedUrl !== false && !empty($aParsedUrl['host'])) {
		if (	$aParsedUrl['host'] !== 'youtube.com'
			&& $aParsedUrl['host'] !== 'www.youtube.com'
			&& $aParsedUrl['host'] !== 'youtu.be'
			&& $aParsedUrl['host'] !== 'www.youtu.be') {
			return false;
		}
		if ($aParsedUrl['host'] === 'youtu.be') {
			$sYoutubeId= str_replace('/', '', $aParsedUrl['path']);
			return $sYoutubeId;
		}
		if ( $aParsedUrl['host'] === 'youtube.com' || $aParsedUrl['host'] === 'www.youtube.com' ) {
			parse_str(parse_url($sYoutubeUrl, PHP_URL_QUERY), $aQueryString);
			return $aQueryString["v"];
		}
	} elseif (preg_match('/^([\w-_]+)$/', $sYoutubeUrl, $matches)) {
		$sYoutubeId = $sYoutubeUrl;
	} else {
		return false;
	}
	return $sYoutubeId;
}
