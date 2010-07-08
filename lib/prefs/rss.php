<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rss_list() {
	return array(
		'rss_basic_auth' => array(
			'name' => tra('RSS basic Authentication'),
			'description' => tra('Propose basic http authentication if the user has no permission to see the feed'),
			'type' => 'flag',
			),
		'rss_cache_time' => array(
			'name' => tra('Caching time'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'shorthint' => tra('seconds'),
			'hint' => tra('Use 0 for no caching'),
			),
		'rss_articles' => array(
			'name' => tra('Articles'),
			'description' => tra('RSS feeds for articles'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_articles',
			),
		),
		'rss_blogs' => array(
			'name' => tra('Blogs'),
			'description' => tra('RSS feeds for blogs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
		),
		'rss_blog' => array(
			'name' => tra('Individual Blogs'),
			'description' => tra('RSS feed for individual blogs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
		),
		'rss_image_galleries' => array(
			'name' => tra('Image galleries'),
			'description' => tra('RSS feed for image galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
		),
		'rss_image_gallery' => array(
			'name' => tra('Individual Image galleries'),
			'description' => tra('RSS feed for individual image galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
		),
		'rss_file_galleries' => array(
			'name' => tra('File galleries'),
			'description' => tra('RSS feed for file galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_file_galleries',
			),
		),
		'rss_file_gallery' => array(
			'name' => tra('Individual file galleries'),
			'description' => tra('RSS feed for individual file galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_file_galleries',
			),
		),
		'rss_wiki' => array(
			'name' => tra('Wiki'),
			'description' => tra('RSS feed for wiki'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
		),
		'rss_forums' => array(
			'name' => tra('Forums'),
			'description' => tra('RSS feed for forums'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
		),
		'rss_forum' => array(
			'name' => tra('Individual Forums'),
			'description' => tra('RSS feed for individual forums'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
		),
		'rss_trackers' => array(
			'name' => tra('Trackers'),
			'description' => tra('RSS feed for trackers'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
		),
		'rss_tracker' => array(
			'name' => tra('Individual trackers items'),
			'description' => tra('RSS feed for individual trackers items'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
		),
		'rss_calendar' => array(
			'name' => tra('Calendar events'),
			'description' => tra('RSS feed for calendar events'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_calendar',
			),
		),
		'rss_directories' => array(
			'name' => tra('Directories'),
			'description' => tra('RSS feed for directories'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_directory',
			),
		),
		'rss_maps' => array(
			'name' => tra('Maps'),
			'description' => tra('RSS feed for maps'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_maps',
			),
		),
		'rss_shoutbox' => array(
			'name' => tra('Shoutbox'),
			'description' => tra('RSS feed for shoutbox'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_shoutbox',
			),
		),
	);
}

