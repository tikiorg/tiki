<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_flash_info()
{
	return array(
		'name' => tra('Flash Video'),
		'documentation' => 'PluginFlash',
		'description' => tra('Embed a video or audio file'),
		'prefs' => array('wikiplugin_flash'),
		'extraparams' => true,
		'tags' => array( 'basic' ),		
		'iconname' => 'video',
		'format' => 'html',
		'introduced' => 1,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Flash Type'),
				'description' => tra('Whether you want to insert a Flash from a URL, a fileId from a podcast file
					gallery or a link to a specific service like Youtube or Vimeo'),
				'since' => '6.1',
				'default' => '',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('Select an option'), 'value' => ''),
					array('text' => tra('Blip.tv'), 'value' => 'bliptv'), 
					array('text' => tra('File Gallery Podcast'), 'value' => 'fileId'),
					array('text' => tra('Movie URL'), 'value' => 'url'),
					array('text' => tra('Vimeo'), 'value' => 'vimeo'),
					array('text' => tra('Youtube'), 'value' => 'youtube'),
				),
			),
			'movie' => array(
				'required' => true,
				'name' => tra('Movie URL'),
				'description' => tr('URL to the movie to include, for example, %0', '<code>files/test.swf</code>'),
				'since' => '1',
				'parent' => array('name' => 'type', 'value' => 'url'),
				'filter' => 'url',
				'default' => '',
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('File Gallery Podcast ID'),
				'description' => tra('ID of a file from a podcast gallery - will work only with podcast gallery'),
				'since' => '5.0',
				'parent' => array('name' => 'type', 'value' => 'fileId'),
				'default' => '',
				'filter' => 'digits',
				'profile_reference' => 'file',
			),
			'youtube' => array(
				'required' => true,
				'name' => tra('YouTube URL'),
				'description' => tra('Complete URL to the YouTube video.') . ' ' . tra('Example:')
					. ' <code>http://www.youtube.com/watch?v=1i2ZnU4iR24</code>',
				'since' => '6.1',
				'parent' => array('name' => 'type', 'value' => 'youtube'),
				'filter' => 'url',
				'default' => '',
			),
			'vimeo' => array(
				'required' => true,
				'name' => tra('Vimeo URL'),
				'description' => tra('Complete URL to the Vimeo video.') . ' ' . tra('Example:')
					. ' <code>http://vimeo.com/3319966</code>',
				'since' => '6.1',
				'parent' => array('name' => 'type', 'value' => 'vimeo'),
				'filter' => 'url',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width of movie in pixels (default is %0)', '<code>425</code>'),
				'since' => '1',
				'advanced' => true,
				'filter' => 'digits',
				'default' => 425,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Height of movie in pixels (default is %0)', '<code>350</code>'),
				'since' => '1',
				'advanced' => true,
				'filter' => 'digits',
				'default' => 350,
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tr('Flash video quality. Default value: %0', '<code>high</code>'),
				'since' => '1',
				'advanced' => true,
				'default' => 'high',
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('High'), 'value' => 'high'), 
					array('text' => tra('Medium'), 'value' => 'medium'), 
					array('text' => tra('Low'), 'value' => 'low'), 
				)
			),
			'altimg' => array(
				'required' => false,
				'name' => tra('Alternative image URL'),
				'description' => tra('Image to display if Flash is not available.'),
				'since' => '10.2',
				'advanced' => true,
				'filter' => 'url',
				'default' => '',
			),
		)
	);
}

function wikiplugin_flash($data, $params)
{
	global $prefs, $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	
	// Handle file from a podcast file gallery
	if (isset($params['fileId']) && !isset($params['movie'])) {
		$filegallib = TikiLib::lib('filegal');
		$file_info = $filegallib->get_file_info($params['fileId']);
		if (!$userlib->user_has_perm_on_object($user, $file_info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
			return tra('Permission denied');
		}
		$params['movie'] = $prefs['fgal_podcast_dir'].$file_info['path'];
	}
	
	// Handle Youtube video
	if (isset($params['youtube']) && preg_match('|http(s)?://(\w+\.)?youtube\.com/watch\?v=([\w-]+)|', $params['youtube'], $matches)) {
		$params['movie'] = "//www.youtube.com/v/" . $matches[3];
	}

	// Handle Vimeo video
	if (isset($params['vimeo']) && preg_match('|http(s)?://(www\.)?vimeo\.com/(clip:)?(\d+)|', $params['vimeo'], $matches)) {
		$params['movie'] = '//vimeo.com/moogaloop.swf?clip_id=' . $matches[4];
	}
	
	if ((isset($params['youtube']) || isset($params['vimeo'])) && !isset($params['movie'])) {
		return tra('Invalid URL');
	}

	unset($params['type']);
	
	$code = $tikilib->embed_flash($params);

	if ( $code === false ) {
		return tra('Missing parameter movie to the plugin flash');
	}
	return $code;
}
