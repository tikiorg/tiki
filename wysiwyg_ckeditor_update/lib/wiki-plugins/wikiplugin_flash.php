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
			'movie' => array(
				'required' => false,
				'name' => tra('Movie URL'),
				'description' => tra('Complete URL to the movie to include. e.g. files/test.swf'),
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('fileId'),
				'description' => tra('Id of a file from a podcast gallery - will work only with podcast gallery'),
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
	if (isset($params['fileId']) && !isset($params['movie'])) {
		global $filegallib; include_once ('lib/filegals/filegallib.php');
		$file_info = $filegallib->get_file_info($params['fileId']);
		if (!$userlib->user_has_perm_on_object($user, $file_info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
			return tra('Permission denied');
		}
		$params['movie'] = $prefs['fgal_podcast_dir'].$file_info['path'];
	}
		
	$code = $tikilib->embed_flash($params);

	if ( $code === false ) {
		return tra('Missing parameter movie to the plugin flash');
	}
	return "~np~$code~/np~";
}
