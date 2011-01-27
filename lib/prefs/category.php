<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_category_list() {
	return array(
		'category_jail' => array(
			'name' => tra('Category Jail'),
			'description' => tra('Limits the visibility of objects to those in these categories. Used mainly for creating workspaces from perspectives.'),
			'separator' => ',',
			'type' => 'text',
			'filter' => 'int',
		),
		'category_defaults' => array(
			'name' => tra('Category Defaults'),
			'description' => tra('Force certain categories to be present. If none of the categories in a given set are provided, assign a category by default.'),
			'type' => 'textarea',
			'filter' => 'striptags',
			'hint' => tra('One per line. ex:1,4,6,7/4'),
			'size' => 5,
			'serialize' => 'prefs_category_serialize_defaults',
			'unserialize' => 'prefs_category_unserialize_defaults',
		),
		'category_i18n_sync' => array(
			'name' => tra('Synchronize multilingual categories'),
			'description' => tra('Make sure that the categories on the translations are synchronized when modified on any version.'),
			'type' => 'list',
			'dependencies' => array( 'feature_multilingual' ),
			'options' => array(
				'n' => tra('None'),
				'whitelist' => tra('Only those specified'),
				'blacklist' => tra('All but those specified'),
			),
		),
		'category_i18n_synced' => array(
			'name' => tra('Synchronized categories'),
			'description' => tra('List of categories affected by the multilingual synchronization. Depending on the parent feature, this list will be used as a white list (only categories allows) or as a black list (all except thoses specified)'),
			'type' => 'text',
			'filter' => 'digits',
			'separator' => ',',
		),
		'category_autogeocode_within' => array(
			'name' => tra('Automatically geocode items when categorized in'),
			'description' => tra('Automatically geocode items based on category name when categorized in the sub-categories of this category ID'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
		),
		'category_autogeocode_replace' => array(
			'name' => tra('Replace existing geocode if any'),
			'description' => tra('When automatically geocoding items based on category name, replace existing geocode if any'),
			'type' => 'flag',		
		),
		'category_autogeocode_fudge' => array(
			'name' => tra('Use approximate geocode location'),
			'description' => tra('When automatically geocoding items based on category name, use randomly approximated location instead of precise location'),
			'type' => 'flag',		
		),
		'category_morelikethis_algorithm' => array(
			'name' => tra('"More Like This" algorithm for categories'),
			'type' => 'list',
			'options' => array(
							   '' => '',
				'basic' => tra('Basic'),
				'weighted' => tra('Weighted'),
			),
		),
		'category_morelikethis_mincommon' => array(
			'name' => tra('Minimum number of categories in common'),
			'type' => 'list',
			'options' => array(
				'1' => tra('1'),
				'2' => tra('2'),
				'3' => tra('3'),
				'4' => tra('4'),
				'5' => tra('5'),
				'6' => tra('6'),
				'7' => tra('7'),
				'8' => tra('8'),
				'9' => tra('9'),
				'10' => tra('10'),
			),
		),
	);
}

function prefs_category_serialize_defaults( $data ) {
	if( ! is_array( $data ) ) {
		$data = unserialize( $data );
	}

	$out = '';
	foreach( $data as $row ) {
		$out .= implode( ',', $row['categories'] ) . '/' . $row['default'] . "\n";
	}

	return trim( $out );
}

function prefs_category_unserialize_defaults( $string ) {
	$data = array();
	
	foreach( explode( "\n", $string ) as $row ) {
		if( preg_match('/^\s*(\d+\s*(,\s*\d+\s*)*)\/\s*(\d+)\s*$/', $row, $parts ) ) {
			$categories = explode( ',', $parts[1] );
			$categories = array_map( 'trim', $categories );
			$categories = array_filter( $categories );
			$default = $parts[3];

			$data[] = array(
				'categories' => $categories,
				'default' => $default,
			);
		}
	}

	if( count( $data ) ) {
		return $data;
	} else {
		return false;
	}
}

