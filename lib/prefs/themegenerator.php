<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_themegenerator_list($partial = false) {
	global $prefs, $themegenlib;

	if (! $partial) {
		include_once 'lib/themegenlib.php';
		
		$themes = array('' => tra('New...'));
		
		$list = $themegenlib->getCurrentTheme()->getPrefList();
		if (count($list) > 0) {
			$list = array_filter($list);
			sort($list);
			foreach( $list as $item ) {
				$tm = new ThemeGenTheme($item);
				$d = $tm->getData();
				if ((empty($d['theme']) || $d['theme'] === $prefs['style']) &&
						(empty($d['theme-option']) || $d['theme-option'] === $prefs['style_option'])) {
					$themes[$item] = $item;
				}
			}
		}
	} else {
		$themes = array();
	}
	
	return array(
		'themegenerator_feature' => array(
			'name' => tra('Theme Generator'),
			'type' => 'flag',
			'warning' => tra('Experimental. This feature is still under development.'),
			'description' => tra('Permits to adjust colors and fonts via the browser.'),
			'help' => 'Theme+Generator',
			'default' => 'n',
		),
		'themegenerator_theme' => array(
			'name' => tra('Custom Theme'),
			'description' => tra(''),
			'type' => 'list',
			'options' => $themes,
			'dependencies' => 'themegenerator_feature',
			'default' => '',
		),
	);	
}
