<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_theme_list()
{
	$themes = [
		'default' => tr('Bootstrap Default'),
		'custom' => tr('Custom theme location, see below'),
		'legacy' => tr('Use legacy styles'),
	];

	foreach (glob("themes/*/css/tiki.css") as $css) {
		$css = dirname(dirname($css));
		$theme = basename($css);
		$themes[$theme] = tr($theme);
	}

	// TODO : Include pre-defined themes
	return array(
		'theme_active' => array(
			'name' => tr('Theme'),
			'description' => tr('Select the theme to be used.'),
			'type' => 'list',
			'default' => 'default',
			'options' => $themes,
		),
		'theme_custom' => array(
			'name' => tr('Custom theme location'),
			'description' => tr('URL of the custom CSS file to include.'),
			'type' => 'text',
			'filter' => 'url',
			'default' => '',
		),
	);
}

