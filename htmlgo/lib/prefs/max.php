<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_max_list() {
	return array(
		'max_username_length' => array(
			'name' => tra('Maximum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_articles' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_blogs' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_blog' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_image_galleries' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_image_gallery' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_file_galleries' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_file_gallery' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_wiki' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_forums' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_forum' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_trackers' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_tracker' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_calendar' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_directories' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_maps' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),

	);	
}
