<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_available_list() {
	global $tikilib;
	$languages = $tikilib->list_languages( false, null, true);
	$map = array();
	
	foreach( $languages as $lang ) {
		$map[ $lang['value'] ] = $lang['name'];
	}

	$all_styles = $tikilib->list_styles();
	$styles = array('' => tra('All'));

	foreach ($all_styles as $style) {
		$styles[$style] = substr( $style, 0, strripos($style, '.css'));
	}

	return array(
		'available_languages' => array(
			'name' => tra('Available languages'),
			'description' => tra('By default, all languages supported by tikiwiki are available on multilingual sites. This option allows to limit the languages to a subset.'),
			'filter' => 'lang',
			'type' => 'multilist',
			'dependencies' => array(
				'feature_multilingual',
			),
			'options' => $map,
			'default' => array(),
		),
		'available_styles' => array(
			'name' => tra('Available styles'),
			'type' => 'multilist',
			'options' => $styles,
			'dependencies' => array(
				'change_theme',
			),
			'default' => array(),
		),
	);
}
