<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_banner_info()
{
	return array(
		'name' => tra('Banner'),
		'documentation' => 'PluginBanner',
		'description' => tra('Add a banner'),
		'prefs' => array('wikiplugin_banner', 'feature_banners'),
		'iconname' => 'bullhorn',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
			'zone' => array(
				'required' => true,
				'name' => tra('Zone'),
				'description' => tra('Name of the zone created in Admin > Banners'),
				'since' => '3.0',
				'default' => '',
			),
			'target' => array(
				'required' => false,
				'name' => tra('Target'),
				'description' => tra('Determines the browser behavior when the banner is clicked'),
				'since' => '3.0',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Blank'), 'value' => '_blank'), 
					array('text' => tra('Display'), 'value' => 'display')
				)
			)
		)
	);
}

function wikiplugin_banner($data, $params)
{
    global $tikilib, $prefs;
	if ($prefs['feature_banners'] != 'y') {
		return;
	}
    $bannerlib = TikiLib::lib('banner');

	extract($params, EXTR_SKIP);
		
    if (empty($zone)) {
        return tra('missing parameter');
    }
	if (empty($target)) {
		$banner = $bannerlib->select_banner($zone);
	} else {
		$banner = $bannerlib->select_banner($zone, $target);
	}
    return '~np~'.$banner.'~/np~';
}
