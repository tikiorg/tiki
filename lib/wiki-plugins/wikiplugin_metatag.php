<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_meta.php 60884 2017-01-12 22:15:32Z luciash $

function wikiplugin_metatag_info()
{
	return array(
		'name' => tra('MetaTag'),
		'documentation' => 'PluginMetaTag',
		'description' => tra('Add custom (meta) tags to HTML head on page where the plugin is used'),
		'prefs' => array( 'wikiplugin_metatag' ),
		'body' => tra('Tags for the HTML head'),
		'validate' => 'all',
		'filter' => 'rawhtml_unsafe',
		'iconname' => 'code',
		'introduced' => 17,
		'tags' => array( 'basic' ),
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name attribute of the meta tag'),
				'since' => '17.0',
				'filter' => 'url',
				'default' => '',
			),
			'content' => array(
				'required' => false,
				'name' => tra('Content'),
				'description' => tra('Content attribute of the meta tag'),
				'since' => '17.0',
				'filter' => 'url',
				'default' => '',
			),
		),
	);
}

function wikiplugin_metatag($data, $params)
{
	$headerlib = TikiLib::lib('header');
	extract($params, EXTR_SKIP);

	if (isset($name)) {
		if (!isset($content)) {
			$content = '';
		}
		$headerlib->add_meta($name,$content);
	} else if ($data) {
		$headerlib->add_rawhtml($data);
	}
	return '';
}
