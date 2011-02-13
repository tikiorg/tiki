<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_mobile_list() {

	$mobile_themes = array(
		'' => tra('Default'),
		'a' => 'A',
		'b' => 'B',
		'c' => 'D',
		'd' => 'D',
		'e' => 'E',
	);

	return array(

		'mobile_feature' => array(
			'name' => tra('Mobile Access'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'warning' => tra('Experimental. This feature is under development.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
		),
		'mobile_perspectives' => array(
			'name' => tra('Mobile Perspectives'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
			'dependencies' => array(
				'mobile_feature',
			),
		),
		'mobile_theme_header' => array(
			'name' => tra('Header Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => 'http://jquerymobile.com/demos/1.0a3/#docs/api/themes.html',
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
		),
		'mobile_theme_content' => array(
			'name' => tra('Content Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => 'http://jquerymobile.com/demos/1.0a3/#docs/api/themes.html',
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
		),
		'mobile_theme_footer' => array(
			'name' => tra('Footer Theme'),
			'hint' => tra('jQuery Mobile Theme'),
			'help' => 'http://jquerymobile.com/demos/1.0a3/#docs/api/themes.html',
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
		),
		'mobile_theme_modules' => array(
			'name' => tra('Modules Theme'),
			'hin' => tra('jQuery Mobile Theme'),
			'help' => 'http://jquerymobile.com/demos/1.0a3/#docs/api/themes.html',
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
		),
		'mobile_theme_menus' => array(
			'name' => tra('Menus Theme'),
			'description' => tra('jQuery Mobile Theme'),
			'help' => 'http://jquerymobile.com/demos/1.0a3/#docs/api/themes.html',
			'type' => 'list',
			'options' => $mobile_themes,
			'dependencies' => array(
				'mobile_feature',
			),
		),
	);
}
