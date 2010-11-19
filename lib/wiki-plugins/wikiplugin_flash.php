<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display a SWF file
// damian aka damosoft 30 March 2004

function wikiplugin_flash_info() {
	return array(
		'name' => tra('Flash video'),
		'documentation' => 'PluginFlash',
		'description' => tra('Displays a Flash (.swf) file in the wiki page'),
		'prefs' => array('wikiplugin_flash'),
		'extraparams' => true,
		'icon' => 'pics/icons/page_white_flash.png',
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Flash type'),
				'description' => tra('Whether you want to insert a Flash from a URL, a fileId from a podcast file gallery or a link to a specific service like Youtube or Vimeo'),
				'options' => array(
					array('text' => tra('Movie URL'), 'value' => 'url'),
					array('text' => tra('Blip.tv'), 'value' => 'bliptv'), 
					array('text' => tra('FileId from a podcast file gallery'), 'value' => 'fileId'),
					array('text' => tra('Vimeo'), 'value' => 'vimeo'),
					array('text' => tra('Youtube'), 'value' => 'youtube'),
				),
			),
			'movie' => array(
				'required' => true,
				'name' => tra('Movie URL'),
				'description' => tra('Complete URL to the movie to include. e.g. files/test.swf'),
				'parent' => array('name' => 'type', 'value' => 'url'),
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('FileId from a podcast file gallery'),
				'description' => tra('Id of a file from a podcast gallery - will work only with podcast gallery'),
				'parent' => array('name' => 'type', 'value' => 'fileId'),
			),
			'youtube' => array(
				'required' => true,
				'name' => tra('Youtube URL'),
				'description' => tra('Entire URL to the YouTube video. Example: http://www.youtube.com/watch?v=1i2ZnU4iR24'),
				'parent' => array('name' => 'type', 'value' => 'youtube'),
			),
			'vimeo' => array(
				'required' => true,
				'name' => tra('Vimeo URL'),
				'description' => tra('Entire URL to the Vimeo video. Example: http://vimeo.com/3319966'),
				'parent' => array('name' => 'type', 'value' => 'youtube'),
			),
			'bliptv' => array(
				'required' => true,
				'name' => tra('Blip.tv URL'),
				'description' => tra('Blip.tv embed URL. Example: http://blip.tv/play/AYGd_GAC'),
				'parent' => array('name' => 'type', 'value' => 'bliptv'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Default width: 425'),
				'advanced' => true,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Default height: 350'),
				'advanced' => true,
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tra('Flash video quality. Default value: high'),
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_flash($data, $params) {
	global $tikilib, $prefs, $userlib, $user;
	
	// Handle file from a podcast file gallery
	if (isset($params['fileId']) && !isset($params['movie'])) {
		global $filegallib; include_once ('lib/filegals/filegallib.php');
		$file_info = $filegallib->get_file_info($params['fileId']);
		if (!$userlib->user_has_perm_on_object($user, $file_info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
			return tra('Permission denied');
		}
		$params['movie'] = $prefs['fgal_podcast_dir'].$file_info['path'];
	}

	// Handle Youtube video
	if (isset($params['youtube']) && preg_match('|http(s)?://(\w+\.)?youtube\.com/watch\?v=([\w-]+)|', $params['youtube'], $matches)) {
		$params['movie'] = "http://www.youtube.com/v/" . $matches[3];
	}

	// Handle Vimeo video
	if (isset($params['vimeo']) && preg_match('|http(s)?://(www\.)?vimeo\.com/(clip:)?(\d+)|', $params['vimeo'], $matches)) {
		$params['movie'] = 'http://vimeo.com/moogaloop.swf?clip_id=' . $matches[4];
	}
	
	// Handle Blip.tv video
	// We need the embed URL because there is tno relation between the video URL and the embed URL
	if (isset($params['bliptv']) && preg_match('|http://blip.tv/play/\w+|', $params['bliptv'], $matches)) {
		$params['movie'] = $params['bliptv'];
	}	

	if ((isset($params['youtube']) || isset($params['vimeo']) || isset($params['bliptv'])) && !isset($params['movie'])) {		
		return tra('Invalid URL');
	}
		
	$code = $tikilib->embed_flash($params);

	if ( $code === false ) {
		return tra('Missing parameter movie to the plugin flash');
	}
	return "~np~$code~/np~";
}
