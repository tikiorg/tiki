<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_flash.php');

function wikiplugin_bliptv_info()
{
	return array(
		'name' => tra('Bliptv'),
		'documentation' => 'PluginBlipTV',
		'description' => tra('Embed a Blip.tv video'),
		'prefs' => array( 'wikiplugin_bliptv' ),
		'iconname' => 'tv',
		'introduced' => 6.1,
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tr('Blip.tv embed URL. Example: %0', '<code>http://blip.tv/play/AYGd_GAC</code>'),
				'since' => '6.1',
				'filter' => 'url',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels'),
				'since' => '6.1',
				'filter' => 'digits',
				'default' => 425,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels'),
				'since' => '6.1',
				'filter' => 'digits',
				'default' => 350,
			),
		),
	);
}

function wikiplugin_bliptv($data, $params)
{
	if (isset($params['url'])) {
		$params['bliptv'] = $params['url'];
		unset($params['movie']);
	}
	
	return wikiplugin_flash($data, $params);
}
