<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_blog_list()
{
	return [
		'blog_comments_per_page' => [
			'name' => tra('Default number per page'),
			'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'units' => tra('comments'),
			'default' => 0,
		],
		'blog_comments_default_ordering' => [
			'name' => tra('Default ordering'),
			'description' => tra(''),
			'type' => 'list',
			'options' => [
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			],
			'default' => 'commentDate_asc',
		],
		'blog_list_order' => [
			'name' => tra('Default order'),
			'description' => tra(''),
			'type' => 'list',
			'options' => [
				'created_desc' => tra('Creation Date (desc)'),
				'lastModif_desc' => tra('Last modification date (desc)'),
				'title_asc' => tra('Blog title (asc)'),
				'posts_desc' => tra('Number of posts (desc)'),
				'hits_desc' => tra('Visits (desc)'),
				'activity_desc' => tra('Activity (desc)'),
			],
			'default' => 'created_desc',
		],
		'blog_list_title' => [
			'name' => tra('Title'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_title_len' => [
			'name' => tra('Title length'),
			'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'units' => tra('characters'),
			'default' => '35',
		],
		'blog_list_description' => [
			'name' => tra('Description'),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_created' => [
			'name' => tra('Creation date'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_lastmodif' => [
			'name' => tra('Last modified'),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_user' => [
			'name' => tra('User'),
			'description' => tra(''),
			'type' => 'list',
			'options' => [
				'disabled' => tra('Disabled'),
				'text' => tra('Plain text'),
				'link' => tra('Link to user information'),
				'avatar' => tra('User profile picture'),
			],
			'default' => 'text',
		],
		'blog_list_posts' => [
			'name' => tra('Posts'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_visits' => [
			'name' => tra('Visits'),
			'type' => 'flag',
			'default' => 'y',
		],
		'blog_list_activity' => [
			'name' => tra('Activity'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		],
		'blog_sharethis_publisher' => [
			'name' => tra('Your ShareThis publisher identifier (optional)'),
			'description' => tra('Set to define your ShareThis publisher identifier'),
			'type' => 'text',
			'size' => '40',
			'default' => '',
		],
		'blog_feature_copyrights' => [
			'name' => tra('Blog post copyright'),
			'description' => tra('Allows for addition of individual copyright notices on blog posts'),
			'type' => 'flag',
			'dependencies' => [
				'feature_blogs',
			],
			'default' => 'n',
		],
	];
}
