<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_theme_list()
{
	$themes = [
		'default' => tr('Bootstrap default'),
//		'legacy' => tr('Bootstrap themes in the "styles" directory'),
		'custom' => tr('Custom bootstrap theme by specifying URL'),
	];

	foreach (glob("themes/*/css/tiki.css") as $css) {
		$css = dirname(dirname($css));
		$theme = basename($css);
		$themes[$theme] = tr($theme);
	}
	
	//get list of base iconsets
	$iconsets = array();
	foreach (scandir('themes/base_files/iconsets') as $iconset_file) {
		if ($iconset_file[0] != '.' && $iconset_file != 'index.php') {
			include('themes/base_files/iconsets/'. $iconset_file);
			$iconsets[substr($iconset_file,0,-4)] = $settings['iconset_name'];
		}
	}
	
	// TODO : Include pre-defined themes
	return array(
		'theme_active' => array(
			'name' => tr('Theme selection'),
			'description' => tr('For more information about Bootstrap, see getbootstrap.com. Themes in the styles directory are bootstrap.css variants including updated legacy Tiki themes as well as themes from Bootswatch.com (for example, select <em>Theme: <b>bootswatch_themes</b>, Theme option: <code>Amelia-bootstrap.min.</code></em>'),
			'type' => 'list',
			'default' => 'default',
			'options' => $themes,
            'help' => 'Tiki13#Themes',
			'tags' => array('basic'),
		),
		'theme_custom' => array(
			'name' => tr('Custom theme URL'),
			'description' => tr('Local or external URL of the custom Bootstrap-compatible CSS file to use.'),
			'type' => 'text',
			'filter' => 'url',
			'default' => '',
			'tags' => array('basic'),
		),
		'theme_iconset' => array(
			'name' => tr('Icons'),
			'description' => tr('Icon set used by the site.'),
			'type' => 'list',
			'options' => $iconsets,
			'default' => 'default',
			'help' => 'Icons',
			'tags' => array('basic'),
		),
	);
}