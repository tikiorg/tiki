<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_available_list($partial = false)
{
	global $tikilib;
	$themelib = TikiLib::lib('theme');
	$map = [];
	$themes = [
		'' => tra('All'),
		'default' => tr('Default Bootstrap'),
	];

	if (! $partial) {
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages(false, null, true);
		foreach ($languages as $lang) {
			$map[ $lang['value'] ] = $lang['name'];
		}

		$themes = $themes + $themelib->list_themes_and_options();
		unset($themes['custom_url']); //make sure Custom URL is removed from the list
		$themes = array_map('ucfirst', $themes); //make first character of array values uppercase
	}

	return [
		'available_languages' => [
			'name' => tra('Available languages'),
			'description' => tra('By default, all languages supported by Tiki are available on multilingual sites. This option allows limiting the languages to a subset.'),
			'filter' => 'lang',
			'type' => 'multilist',
			'dependencies' => [
				'feature_multilingual',
				'restrict_language',
			],
			'tags' => ['basic'],
			'options' => $map,
			'default' => [],
		],
		'available_themes' => [
			'name' => tra('Available themes'),
			'description' => tra('Restrict available themes'),
			'type' => 'multilist',
			'options' => $themes,
			'dependencies' => [
				'change_theme',
			],
			'default' => [],
		],
	];
}
