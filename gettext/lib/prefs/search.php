<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_search_list() {
	return array (
		'search_refresh_index_mode' => array(
			'name' => tra('Refresh mode'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'normal' => tra('Normal'),
				'random' => tra('Random'),
			),
		),
		'search_refresh_rate' => array(
			'name' => tra('Refresh rate'),
			'hint' => tra('Use 0 for no refresh'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_min_wordlength' => array(
			'name' => tra('Minimum length of search word'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_max_syllwords' => array(
			'name' => tra('Max. number of words containing a syllable'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_syll_age' => array(
			'name' => tra('Max. age in hours of syllable search cache'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_lru_purge_rate' => array(
			'name' => tra('LRU list purging rate'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_lru_length' => array(
			'name' => tra('LRU list length'),
			'type' => 'text',
			'size' => '4',
			'perspective' => false,
		),
		'search_parsed_snippet' => array(
			'name' => tra('Parse the results'),
			'hint' => tra('May impact performance'),
			'type' => 'flag',
		),
		'search_default_where' => array(
			'name' => tra('Default where'),
			'description' => tra('When object filter is not on, limit to search one type of object'),
			'type' => 'list',
			'options' => array(
				'' => tra('Entire site'),
				'wikis' => tra('Wiki Pages'),
				'trackers' => tra('Trackers'),
			),
		),
		'search_default_interface_language' => array(
			'name' => tra('Restrict search language by default'),
			'description' => tra('If enabled, only search content in the interface language, by default.'),
			'type' => 'flag',
		),
		'search_autocomplete' => array(
			'name' => tra('Autocomplete on page names'),
			'type' => 'flag',
			'dependencies' => array('feature_jquery_autocomplete', 'javascript_enabled'),
		),
	);
}
