<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_banner_info()
{
	return [
		'name' => tra('Banner'),
		'documentation' => 'PluginBanner',
		'description' => tra('Add a banner'),
		'prefs' => ['wikiplugin_banner', 'feature_banners'],
		'iconname' => 'bullhorn',
		'introduced' => 3,
		'tags' => [ 'basic' ],
		'params' => [
			'zone' => [
				'required' => true,
				'name' => tra('Zone'),
				'description' => tra('Name of the zone created in Admin > Banners'),
				'since' => '3.0',
				'default' => '',
			],
			'target' => [
				'required' => false,
				'name' => tra('Target'),
				'description' => tra('Determines the browser behavior when the banner is clicked'),
				'since' => '3.0',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Blank'), 'value' => '_blank'],
					['text' => tra('Display'), 'value' => 'display']
				]
			]
		]
	];
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
	return '~np~' . $banner . '~/np~';
}
