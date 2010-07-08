<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_desc_list() {
	return array(
		'desc_rss_articles' => array(
			'name' => tra('Article RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for articles.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_blogs' => array(
			'name' => tra('Blogs RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for blogs.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_blog' => array(
			'name' => tra('Blog RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual blogs.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_image_galleries' => array(
			'name' => tra('Image galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for image galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_image_gallery' => array(
			'name' => tra('Individual image galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual image galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_file_galleries' => array(
			'name' => tra('File galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for file galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_file_gallery' => array(
			'name' => tra('Individual file galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual file galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_wiki' => array(
			'name' => tra('Wiki pages RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for wiki pages.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_forums' => array(
			'name' => tra('Forums RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for forums.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_forum' => array(
			'name' => tra('Individual forums RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual forums.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_trackers' => array(
			'name' => tra('Trackers RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for trackers.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_tracker' => array(
			'name' => tra('Individual trackers RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual trackers.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_calendar' => array(
			'name' => tra('Calendar events RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for calendar events.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_directories' => array(
			'name' => tra('Directories RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for directories.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_maps' => array(
			'name' => tra('Maps RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for maps.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'desc_rss_shoutbox' => array(
			'name' => tra('Shoutbox RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for shoutbox messages.'),
			'type' => 'textarea',
			'size' => 2,
		),
	);
}
