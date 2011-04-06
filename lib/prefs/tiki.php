<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
			'default' => 604800,
		),
		'tiki_minify_javascript' => array(
			'name' => tra('Minify JavaScript'),
			'description' => tra('Compress JavaScript files used in the page into a single file to be distributed statically. Changes to JavaScript files will require cache to be cleared. Uses http://code.google.com/p/minify/'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'tiki_minify_css' => array(
			'name' => tra('Minify CSS'),
			'description' => tra('Compress CSS files by removing additional spaces and grouping multiple files into one. Changes to CSS files will require cache to be cleared. Uses http://code.google.com/p/minify/'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'tiki_minify_css_single_file' => array(
			'name' => tra('Minify CSS into a single file'),
			'description' => tra('In addition to reducing the size of the CSS, reduce the number of included files.'),
			'type' => 'flag',
			'perspective' => false,
			'warning' => tra('This setting may not work out of the box for all styles. import needs to use @import url("...") and not @import "..."'),
			'default' => 'n',
		),
		'tiki_same_day_time_only' => array(
			'name' => tra('Skip date for same day'),
			'description' => tra('When displaying short date and time, skip date for today. Only time will be displayed.'),
			'type' => 'flag',
			'default' => 'y',
		),
		'tiki_cachecontrol_session' => array(
			'name' => tra('Cache-Control header'),
			'description' => tra('Custom HTTP header to use when a session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'hint' => tra('Example: no-cache, pre-check=0, post-check=0'),
			'default' => '',
		),
		'tiki_cachecontrol_nosession' => array(
			'name' => tra('Cache-Control header (no session)'),
			'description' => tra('Custom HTTP header to use when no session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'dependencies' => array( 'session_silent' ),
			'default' => '',
		),
		'tiki_cdn' => array(
			'name' => tra('Content Delivery Network'),
			'description' => tra('Use an alternate domain name to serve static files from tikiwiki to avoid sending cookies, improve local caching and generally improve user experience performance.'),
			'hint' => tra('Prefix to include before the static files, for example: http://cdn.example.com'),
			'help' => 'Content+Delivery+Network',
			'type' => 'text',
			'size' => 40,
			'filter' => 'url',
			'default' => '',
		),
		'tiki_cdn_ssl' => array(
			'name' => tra('Content Delivery Network (in SSL)'),
			'description' => tra('Use an alternate domain name to serve static files from tikiwiki to avoid sending cookies, improve local caching and generally improve user experience performance. Leave empty to disable CDN in SSL mode.'),
			'hint' => tra('Prefix to include before the static files, for example: https://cdn.example.com'),
			'help' => 'Content+Delivery+Network',
			'type' => 'text',
			'size' => 40,
			'filter' => 'url',
			'default' => '',
		),
		'tiki_domain_prefix' => array(
			'name' => tra('Domain prefix handling'),
			'description' => tra('Strip or automatically add the www. prefix on domain names to unify URLs.'),
			'type' => 'list',
			'options' => array(
				'unchanged' => tra('Leave as-is'),
				'strip' => tra('Remove the www'),
				'force' => tra('Add the www'),
			),
			'default' => 'unchanged',
		),
		'tiki_domain_redirects' => array(
			'name' => tra('Domain redirects'),
			'description' => tra('When the site is accessed through specific domain names, redirect to an alternate domain preserving the URL. Useful for domain name transitions, like tikiwiki.org to tiki.org.'),
			'type' => 'textarea',
			'hint' => tra('One entry per line. Comma separated list: old, new'),
			'size' => 8,
			'default' => '',
		),
	);
}
