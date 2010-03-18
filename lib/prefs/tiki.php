<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tiki_list() {
	return array(
		'tiki_version_check_frequency' => array(
			'name' => tra('Check frequency'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'86400' => tra('Each day'),
				'604800' => tra('Each week'),
				'2592000' => tra('Each month'),
			),
			'dependencies' => array(
				'feature_version_checks',
			),
		),
		'tiki_minify_javascript' => array(
			'name' => tra('Minify javascript'),
			'description' => tra('Compress javascript files used in the page into a single file to be distributed statically. Changes to javascript files will require cache to be cleared.'),
			'type' => 'flag',
			'perspective' => false,
		),
		'tiki_minify_css' => array(
			'name' => tra('Minify CSS'),
			'description' => tra('Compress CSS files by removing additional spaces and grouping multiple files into one. Changes to CSS files will require cache to be cleared.'),
			'type' => 'flag',
			'perspective' => false,
		),
		'tiki_minify_css_single_file' => array(
			'name' => tra('Minify CSS into a single file'),
			'description' => tra('In addition to reducing the size of the CSS, reduce the amount of included files.'),
			'type' => 'flag',
			'perspective' => false,
			'warning' => tra('This setting may not work out of the box for all styles.'),
		),
		'tiki_same_day_time_only' => array(
			'name' => tra('Skip date for same day'),
			'description' => tra('When displaying short date and time, skip date for today. Only time will be displayed.'),
			'type' => 'flag',
		),
		'tiki_cachecontrol_session' => array(
			'name' => tra('Cache-Control header'),
			'description' => tra('Custom HTTP header to use when a session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'hint' => tra('Example: no-cache, pre-check=0, post-check=0'),
		),
		'tiki_cachecontrol_nosession' => array(
			'name' => tra('Cache-Control header (no session)'),
			'description' => tra('Custom HTTP header to use when no session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'dependencies' => array( 'session_silent' ),
		),
	);
}
