<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_themegenerator_list() {
	global $prefs, $themegenlib;
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
	
	return array(
		'themegenerator_theme' => array(
			'name' => tra('Custom Theme'),
			'description' => tra(''),
			'type' => 'list',
			'options' => $themes,
			'dependencies' => 'feature_themegenerator',
		),
	);	
}
