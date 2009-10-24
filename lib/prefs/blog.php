<?php

function prefs_blog_list() {
	return array(
		'blog_spellcheck' => array(
			'name' => 'Spell cheking',
			'type' => 'flag',
			'help' => 'Spellcheck',
		),
		'blog_comments_per_page' => array(
			'name' => tra('Default number per page'),
			'type' => 'text',
			'size' => '3',
		),
		'blog_comments_default_ordering' => array(
			'name' => 'Default ordering',
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
		),
		'blog_list_order' => array(
			'name' => 'Default ordering',
			'type' => 'list',
			'options' => array(
				'created_desc' => tra('Creation date (desc)'),
				'lastModif_desc' => tra('Last modification date (desc)'),
				'title_asc' => tra('Blog title (asc)'),
				'posts_desc' => tra('Number of posts (desc)'),
				'hits_desc' => tra('Visits (desc)'),
				'activity_desc' => tra('Activity (desc)'),
			),
		),
		'blog_list_title' => array(
			'name' => 'Title',
			'type' => 'flag',
		),
		'blog_list_title_len' => array(
			'name' => tra('Title length'),
			'type' => 'text',
			'size' => '3',
		),
		'blog_list_description' => array(
			'name' => 'Description',
			'type' => 'flag',
		),
		'blog_list_created' => array(
			'name' => 'Creation date',
			'type' => 'flag',
		),
		'blog_list_lastmodif' => array(
			'name' => 'Last modified',
			'type' => 'flag',
		),
		'blog_list_user' => array(
			'name' => 'User',
			'type' => 'list',
			'options' => array(
				'disabled' => tra('Disabled'),
				'text' => tra('Plain text'),
				'link' => tra('Link to user information'),
				'avatar' => tra('User avatar'),
			),
		),
		'blog_list_posts' => array(
			'name' => 'Posts',
			'type' => 'flag',
		),
		'blog_list_visits' => array(
			'name' => 'Visits',
			'type' => 'flag',
		),
		'blog_list_activity' => array(
			'name' => 'Activity',
			'type' => 'flag',
		),
	);
}
