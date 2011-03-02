<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_feed_list() {
	return array(
		'feed_default_version' => array(
			'name' => tra('Default feed format'),
			'type' => 'list',
			'options' => array(
				'5' => tra('ATOM 1.0'),
				'2' => tra('RSS 2.0'),
			),
		),

		// atom specific preferences
		'feed_atom_author_name' => array(
			'name' => tra('Feed author name'),
			'type' => 'text',
			'size' => '40',
			'hint' => tra('This field is mandatory unless both feed author email and homepage are empty.'),
		),
		'feed_atom_author_email' => array(
			'name' => tra('Feeed author email'),
			'type' => 'text',
			'size' => '40',
		),
		'feed_atom_author_url' => array(
			'name' => tra('Feed author homepage'),
			'type' => 'text',
			'size' => '40',
		),

		// rss specific preferences
		'feed_rss_editor_email' => array(
			'name' => tra('Feed editor email'),
			'description' => tra('Email address for person responsible for editorial content.'),
			'type' => 'text',
			'size' => '40',
		),
		'feed_rss_webmaster_email' => array(
			'name' => tra('Feed webmaster email'),
			'description' => tra('Email address for person responsible for technical issues relating to channel.'),
			'type' => 'text',
			'size' => '40',
		),

		'feed_img' => array(
			'name' => tra('Image path'),
			'description' => tra('Specifies a GIF, JPEG or PNG image that can be displayed with the feed.'),
			'type' => 'text',
			'size' => '40',
		),
		'feed_language' => array(
			'name' => tra('Language'),
			'type' => 'text',
			'size' => '10',
		),
		'feed_basic_auth' => array(
			'name' => tra('RSS basic Authentication'),
			'description' => tra('Propose basic http authentication if the user has no permission to see the feed'),
			'type' => 'flag',
			),
		'feed_cache_time' => array(
			'name' => tra('Caching time'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'shorthint' => tra('seconds'),
			'hint' => tra('Use 0 for no caching'),
			),
		'feed_articles' => array(
			'name' => tra('Articles'),
			'description' => tra('RSS feeds for articles'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_articles',
			),
		),
		'feed_blogs' => array(
			'name' => tra('Blogs'),
			'description' => tra('RSS feeds for blogs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
		),
		'feed_blog' => array(
			'name' => tra('Individual Blogs'),
			'description' => tra('RSS feed for individual blogs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
		),
		'feed_image_galleries' => array(
			'name' => tra('Image galleries'),
			'description' => tra('RSS feed for image galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
		),
		'feed_image_gallery' => array(
			'name' => tra('Individual Image galleries'),
			'description' => tra('RSS feed for individual image galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
		),
		'feed_file_galleries' => array(
			'name' => tra('File Galleries'),
			'description' => tra('RSS feed for file galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_file_galleries',
			),
		),
		'feed_file_gallery' => array(
			'name' => tra('Individual file galleries'),
			'description' => tra('RSS feed for individual file galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_file_galleries',
			),
		),
		'feed_wiki' => array(
			'name' => tra('Wiki'),
			'description' => tra('RSS feed for wiki'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
		),
		'feed_forums' => array(
			'name' => tra('Forums'),
			'description' => tra('RSS feed for forums'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
		),
		'feed_forum' => array(
			'name' => tra('Individual Forums'),
			'description' => tra('RSS feed for individual forums'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
		),
		'feed_trackers' => array(
			'name' => tra('Trackers'),
			'description' => tra('RSS feed for trackers'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
		),
		'feed_tracker' => array(
			'name' => tra('Individual trackers items'),
			'description' => tra('RSS feed for individual trackers items'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
		),
		'feed_calendar' => array(
			'name' => tra('Calendar events'),
			'description' => tra('RSS feed for calendar events'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_calendar',
			),
		),
		'feed_directories' => array(
			'name' => tra('Directories'),
			'description' => tra('RSS feed for directories'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_directory',
			),
		),
		'feed_maps' => array(
			'name' => tra('Maps'),
			'description' => tra('RSS feed for maps'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_maps',
			),
		),
		'feed_shoutbox' => array(
			'name' => tra('Shoutbox'),
			'description' => tra('RSS feed for shoutbox'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_shoutbox',
			),
		),
		'feed_articles_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_blogs_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_blog_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_image_galleries_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_image_gallery_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_file_galleries_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_file_gallery_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_wiki_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_forums_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_forum_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_trackers_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_tracker_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_calendar_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_directories_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_maps_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_shoutbox_max' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'feed_articles_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_blogs_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_blog_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_image_galleries_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_image_gallery_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_file_galleries_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_file_gallery_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_wiki_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_forums_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_forum_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_trackers_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_tracker_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_calendar_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_directories_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_maps_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_shoutbox_showAuthor' => array(
			'name' => tra('Show Author'),
			'type' => 'flag',
		),
		'feed_articles_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_blogs_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_blog_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_image_galleries_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_image_gallery_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_file_galleries_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_file_gallery_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_wiki_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_forum_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_trackers_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_tracker_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_calendar_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_directories_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_maps_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_shoutbox_homepage' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => '60',
		),
		'feed_articles_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_blogs_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_blog_title' => array(
			'name' => tra('Title'),
			'desc' => tra('Title to be prepended to the blog title for all blogs. If this field is empty only the blog title will be used.'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_image_galleries_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_image_gallery_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_file_galleries_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_file_gallery_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_wiki_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_forums_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_forum_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_trackers_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_tracker_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_calendar_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_directories_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_maps_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_shoutbox_title' => array(
			'name' => tra('Title'),
			'type' => 'text',
			'size' => '80',
		),
		'feed_articles_desc' => array(
			'name' => tra('Article RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for articles.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_blogs_desc' => array(
			'name' => tra('Blogs RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for blogs.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_blog_desc' => array(
			'name' => tra('Blog RSS Description'),
			'description' => tra('Description to be prepended to the blog description and published as part of the RSS feed for individual blogs. If this field is empty only the blog description will be used.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_image_galleries_desc' => array(
			'name' => tra('Image galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for image galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_image_gallery_desc' => array(
			'name' => tra('Individual image galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual image galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_file_galleries_desc' => array(
			'name' => tra('File galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for file galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_file_gallery_desc' => array(
			'name' => tra('Individual file galleries RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual file galleries.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_wiki_desc' => array(
			'name' => tra('Wiki pages RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for wiki pages.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_forums_desc' => array(
			'name' => tra('Forums RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for forums.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_forum_desc' => array(
			'name' => tra('Individual forums RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual forums.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_trackers_desc' => array(
			'name' => tra('Trackers RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for trackers.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_tracker_desc' => array(
			'name' => tra('Individual trackers RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for individual trackers.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_calendar_desc' => array(
			'name' => tra('Calendar events RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for calendar events.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_directories_desc' => array(
			'name' => tra('Directories RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for directories.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_maps_desc' => array(
			'name' => tra('Maps RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for maps.'),
			'type' => 'textarea',
			'size' => 2,
		),
		'feed_shoutbox_desc' => array(
			'name' => tra('Shoutbox RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for shoutbox messages.'),
			'type' => 'textarea',
			'size' => 2,
		),
	);
}

