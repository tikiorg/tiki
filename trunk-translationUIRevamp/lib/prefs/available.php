<?php

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
		),
		'available_styles' => array(
			'name' => tra('Available styles'),
			'type' => 'multilist',
			'options' => $styles,
			'dependencies' => array(
				'change_theme',
			),
		),
	);
}
