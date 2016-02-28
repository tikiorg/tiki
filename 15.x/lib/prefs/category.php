<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_category_list()
{
	return array(
		'category_jail' => array(
			'name' => tra('Category jail'),
			'description' => tra('Limits the visibility of objects to those in these category IDs. Used mainly for creating workspaces from perspectives.'),
			'separator' => ',',
			'type' => 'text',
			'filter' => 'int',
			'default' => array(''), //empty string needed to keep preference from setting unexpectedly
			'detail' => tra('This should only be set for perspectives, and not globally.'),
			'profile_reference' => 'category',
		),
		'category_jail_root' => array(
			'name' => tra('Category jail root'),
			'description' => tra('Always display categories outside of the jail root, which would be for normal categorization.'),
			'separator' => ',',
			'type' => 'text',
			'filter' => 'int',
			'default' => array(0), //empty string needed to keep preference from setting unexpectedly
			'profile_reference' => 'category',
		),
		'category_defaults' => array(
			'name' => tra('Category defaults'),
			'description' => tra('Require certain categories to be present. If none of the categories in a given set is provided, assign a category by default.').' '.tra('Use *7 to specify all the categories in the subtree of 7 + category 7.').' '.tra('Can do only this for objectname matching the regex (Example: /^RND_/ = name beginning by RND_)(Optional)').' '.tra('Can do for wiki only (optional).').' '.tra('Rename will only reassign the categories for wiki pages.'),
			'type' => 'textarea',
			'filter' => 'striptags',
			'hint' => tra('One per line, for example: 1,4,6,*7/4:/^RND_/:wiki page'),
			'size' => 5,
			'serialize' => 'prefs_category_serialize_defaults',
			'unserialize' => 'prefs_category_unserialize_defaults',
			'profile_reference' => 'category',
			'default' => false,
		),
		'category_i18n_sync' => array(
			'name' => tra('Synchronize multilingual categories'),
			'description' => tra('Make sure that the categories of the translations are synchronized when modified on any version.'),
			'type' => 'list',
			'dependencies' => array( 'feature_multilingual' ),
			'options' => array(
				'n' => tra('None'),
				'whitelist' => tra('Only those specified'),
				'blacklist' => tra('All but those specified'),
			),
			'default' => 'n',
		),
		'category_i18n_synced' => array(
			'name' => tra('Synchronized categories'),
			'description' => tra('List of categories affected by the multilingual synchronization. Depending on the parent feature, this list will be used as a white list (the only categories allowed) or as a black list (all categories allowed except those specified).'),
			'type' => 'text',
			'filter' => 'digits',
			'separator' => ',',
			'default' => array(''), //empty string needed to keep preference from setting unexpectedly
		),
		'category_sort_ascii' => array(
			'name' => tra('Sort categories by legacy ASCII sequence'),
			'description' => tra('If turned on, will sort categories taking into account all characters as upper case, in ASCII sequence, will only consider limited set of accented characters. Might be needed to be able to override the normal sort by prepending punctuation characters to category names, such as "-" to sort at the beginning and "[" to sort at the end.'), 
			'type' => 'flag',
			'tags' => array('advanced'),
			'default' => 'n',
		),
		'category_autogeocode_within' => array(
			'name' => tra('Automatically geocode items when categorized in'),
			'description' => tra('Automatically geocode items based on category name when categorized in the sub-categories of this category ID'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
			'default' => '',
		),
		'category_autogeocode_replace' => array(
			'name' => tra('Replace existing geocode, if any'),
			'description' => tra('When automatically geocoding items based on category name, replace existing geocode, if any'),
			'type' => 'flag',		
			'default' => 'n',
		),
		'category_autogeocode_fudge' => array(
			'name' => tra('Use approximate geocode location'),
			'description' => tra('When automatically geocoding items based on category name, use randomly approximated location instead of precise location'),
			'type' => 'flag',		
			'default' => 'n',
		),
		'category_morelikethis_algorithm' => array(
			'name' => tra('"More Like This" algorithm for categories'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
							   '' => '',
				'basic' => tra('Basic'),
				'weighted' => tra('Weighted'),
			),
			'default' => '',
		),
		'category_morelikethis_mincommon' => array(
			'name' => tra('Minimum number of categories in common'),
            'description' => tra(''),
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
			'default' => 2, 
		),
		'category_morelikethis_mincommon_orless' => array(
			'name' => tra('Or look for the maximum less categories in common if no objects with the above number of common categories'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		),
		'category_morelikethis_mincommon_max' => array(
			'name' => tra('Maximum number of "more like this" objects; otherwise, use the default maximum records'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'default' => 0,
		),
		'category_custom_facets' => array(
			'name' => tr('Generate custom facets from categories'),
			'description' => tr('Comma-separated list of category IDs.'),
			'type' => 'text',
			'size' => 15,
			'filter' => 'int',
			'separator' => ',',
			'default' => '',
		),
		'category_browse_count_objects' => array(
			'name' => tra('Show category object count'),
            'description' => tra('Show object count when browsing categories, complying with search and type filters'),
			'hint' => tra('Can slow the loading of the categories page on large sites.'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}

function prefs_category_serialize_defaults( $data )
{
	if ( ! is_array($data) ) {
		$data = unserialize($data);
	}

	$out = '';
	if ( is_array($data) ) {
		foreach ( $data as $row ) {
			$out .= implode(',', $row['categories']) . '/' . $row['default'];
			if (!empty($row['filter'])) {
				$out .= ':' . $row['filter'];
			}
			if (!empty($row['type'])) {
				if (empty($row['filter'])) {
					$out .= ':';
				}
				$out .= ':'. $row['type'];
			}
			$out .= "\n";
		}
	}

	return trim($out);
}

function prefs_category_unserialize_defaults( $string )
{
	$data = array();
	
	foreach ( explode("\n", $string) as $row ) {
		if ( preg_match('/^\s*(\*?\d+\s*(,\s*\*?\d+\s*)*)\/\s*(\d+)\s*(:(.*)(:(wiki page))?)?$/U', $row, $parts) ) {
			$categories = explode(',', $parts[1]);
			$categories = array_map('trim', $categories);
			$categories = array_filter($categories);
			$default = $parts[3];
			$filter = empty($parts[5])? '': $parts[5];
			$type = empty($parts[7])? '':'wiki page';

			$data[] = array(
				'categories' => $categories,
				'default' => $default,
				'filter' => $filter,
				'type' => $type,
			);
		}
	}

	if ( count($data) ) {
		return $data;
	} else {
		return false;
	}
}
