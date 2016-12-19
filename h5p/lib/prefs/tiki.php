<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tiki_list()
{
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
			'tags' => array('basic'),
		),
		'tiki_release_cycle' => array(
			'name' => tr('Upgrade cycle'),
			'type' => 'list',
			'default' => 'regular',
			'dependencies' => array(
				'feature_version_checks',
			),
			'options' => array(
				'regular' => tr('Regular (6 months)'),
				'longterm' => tr('Long-Term Support'),
			),
			'help' => 'Version+Lifecycle',
		),
		'tiki_minify_javascript' => array(
			'name' => tra('Minify JavaScript'),
			'description' => tra('Compress JavaScript files used in the page into a single file to be distributed statically. Changes to JavaScript files will require cache to be cleared. Uses http://code.google.com/p/minify/'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			'tags' => array('basic'),
		),
		'tiki_minify_late_js_files' => array(
			'name' => tra('Minify Late JavaScript'),
			'description' => tra('Compress extra JavaScript files used in the page after tiki-setup into a separate file which may vary from page to page.'),
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
			'tags' => array('basic'),
		),
		'tiki_minify_css_single_file' => array(
			'name' => tra('Minify CSS into a single file'),
			'description' => tra('In addition to reducing the size of the CSS, reduce the number of included files.'),
			'type' => 'flag',
			'perspective' => false,
			'warning' => tra('This setting may not work out of the box for all styles. Import needs to use @import url("...") and not @import "..."'),
			'default' => 'n',
			'tags' => array('basic'),
		),
		'tiki_same_day_time_only' => array(
			'name' => tra('Skip date for same day'),
			'description' => tra('When displaying short date and time, skip date for today. Only time will be displayed.'),
			'type' => 'flag',
			'default' => 'y',
			'tags' => array('basic'),
		),
		'tiki_cachecontrol_session' => array(
			'name' => tra('Cache-control header'),
			'description' => tra('Custom HTTP header to use when a session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'hint' => tra('Example: no-cache, pre-check=0, post-check=0'),
			'default' => '',
		),
		'tiki_cachecontrol_nosession' => array(
			'name' => tra('Cache-control header (no session)'),
			'description' => tra('Custom HTTP header to use when no session is active.'),
			'type' => 'text',
			'filter' => 'striptags',
			'dependencies' => array( 'session_silent' ),
			'default' => '',
		),
		'tiki_cdn' => array(
			'name' => tra('Content Delivery Networks'),
			'description' => tra('Use alternate domains to serve static files from this Tiki site to avoid sending cookies, improve local caching and generally improve user-experience performance.'),
			'hint' => tra('List of URI prefixes to include before static files (one per line), for example: http://cdn1.example.com'),
			'help' => 'Content+Delivery+Network',
			'type' => 'textarea',
			'size' => 4,
			'filter' => 'url',
			'default' => '',
		),
		'tiki_cdn_ssl' => array(
			'name' => tra('Content Delivery Networks (in SSL)'),
			'description' => tra('Use alternate domains to serve static files from this Tiki site to avoid sending cookies, improve local caching and generally improve user-experience performance. Leave empty to disable CDN in SSL mode.'),
			'hint' => tra('List of URI prefixes to include before static files (one per line), for example: https://sslcdn1.example.com'),
			'help' => 'Content+Delivery+Network',
			'type' => 'textarea',
			'size' => 4,
			'filter' => 'url',
			'default' => '',
		),
		'tiki_domain_prefix' => array(
			'name' => tra('Domain prefix handling'),
			'description' => tra('Strip or automatically add the "www." prefix on domain names to standardize URLs.'),
			'type' => 'list',
			'options' => array(
				'unchanged' => tra('Leave as-is'),
				'strip' => tra('Remove the www'),
				'force' => tra('Add the www'),
			),
			'default' => 'unchanged',
			'tags' => array('basic'),
		),
		'tiki_domain_redirects' => array(
			'name' => tra('Domain redirects'),
			'description' => tra('When the site is accessed through specific domain names, redirect to an alternate domain preserving the URL. Useful for domain name transitions, like tikiwiki.org to tiki.org.'),
			'type' => 'textarea',
			'hint' => tra('One entry per line, with each entry a comma-separated list: old domain, new domain'),
			'size' => 8,
			'default' => '',
		),
		'tiki_check_file_content' => array(
			'name' => tra('Validate uploaded file content'),
			'description' => tra('Do not trust user input and open the files to verify their content.'),
			'type' => 'flag',
			'extensions' => array('fileinfo'),
			'default' => 'y',
		),
		'tiki_allow_trust_input' => array(
			'name' => tra('Allow the tiki_p_trust_input permission.'),
			'hint' => tra('Bypasses user input filtering'),
			'warning' => tra('Note: all permissions are granted to the Admins group including this one, so if you enable this you may expose your site to XSS (Cross Site Scripting) attacks for admin users.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'tiki_object_selector_threshold' => array(
			'name' => tr('Object selector threshold'),
			'description' => tr('Number of records after which the object selectors will request searching instead of selecting from a list.'),
			'type' => 'text',
			'size' => 6,
			'default' => 250,
			'filter' => 'int',
		),
	);
}
