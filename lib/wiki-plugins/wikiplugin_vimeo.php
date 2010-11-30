<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_flash.php');

function wikiplugin_vimeo_info() {
	return array(
		'name' => tra('Vimeo'),
		'documentation' => 'PluginVimeo',
		'description' => tra('Display a Vimeo video'),
		'prefs' => array( 'wikiplugin_vimeo' ),
		'icon' => 'pics/icons/vimeo.png',
		'introduced' => 6.1,
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tra('Entire URL to the Vimeo video. Example: http://vimeo.com/3319966'),
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
				'filter' => 'alpha',
    			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('High'), 'value' => 'high'), 
					array('text' => tra('Medium'), 'value' => 'medium'), 
					array('text' => tra('Low'), 'value' => 'low'), 
				),
				'default' => 'high',
				'advanced' => true				
			),
			'allowFullScreen' => array(
				'required' => false,
				'name' => tra('Full screen'),
				'description' => tra('Expand to full screen'),
				'filter' => 'alpha',
    			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'true'), 
					array('text' => tra('No'), 'value' => 'false'), 
				),
				'default' => '',
				'advanced' => true				
			),
		),
	);
}

function wikiplugin_vimeo($data, $params) {
	if (isset($params['url'])) {
		$params['vimeo'] = $params['url'];
		unset($params['url']);
	}
	
	return wikiplugin_flash($data, $params);
}
