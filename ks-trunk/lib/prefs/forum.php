<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_forum_list()
{
	return array(
		'forum_comments_no_title_prefix' => array(
			'name' => tra("Do not prefix messages titles by 'Re: '"),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_match_regex' => array(
			'name' => tra('Uploaded filenames must match regex'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '20',
			'default' => '',
		),
		'forum_thread_defaults_by_forum' => array(
			'name' => tra('Manage thread defaults per-forum'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_thread_user_settings' => array(
			'name' => tra('Display thread configuration bar'),
            'description' => tra(''),
			'type' => 'flag',
			'hint' => tra('Allows users to override the defaults'),
			'default' => 'y',
		),
		'forum_thread_user_settings_threshold' => array(
			'name' => tra('Display thread configuration bar only when number of posts exceed'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 10,
                ),
		'forum_thread_user_settings_keep' => array(
			'name' => tra('Keep settings for all forums during the user session'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_comments_per_page' => array(
			'name' => tra('Default number per page'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 20,
		),
		'forum_thread_style' => array(
			'name' => tra('Default style'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
				'commentStyle_plain' => tra('Plain'),
				'commentStyle_threaded' => tra('Threaded'),
				'commentStyle_headers' => tra('Headers Only'),
			),
			'default' => 'commentStyle_plain',
		),
		'forum_thread_sort_mode' => array(
			'name' => tra('Default sort mode'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Score'),
				'title_desc' => tra('Title (desc)'),
				'title_asc' => tra('Title (asc)'),
			),
			'default' => 'commentDate_asc',
		),
		'forum_list_topics' => array(
			'name' => tra('Topics'),
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'n',
		),
		'forum_list_posts' => array(
			'name' => tra('Posts'),
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_ppd' => array(
			'name' => tra('Posts per day') . ' (PPD)',
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'n',
		),
		'forum_list_lastpost' => array(
			'name' => tra('Last post'),
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_visits' => array(
			'name' => tra('Visits'),
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_desc' => array(
			'name' => tra('Description'),
            'description' => tra(''),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_description_len' => array(
			'name' => tra('Description length'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '240',
		),
		'forum_reply_notitle' => array(
			'name' => tra('Hide titles for forum threads'),
			'description' => tra('Most titles are left untouched because they are a direct reply to the parent thread. This feature hides the title altogether from the forms and display.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_reply_forcetitle' => array(
			'name' => tra('Force title in reply'),
			'description' => tra('Shows a blank title box and requires it to be filled before submission of forum post.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_available_categories' => array(
			'name' => tr('Forum post categories'),
			'description' => tr('Categories available in the category picker for forum posts.'),
			'type' => 'text',
			'separator' => ',',
			'filter' => 'digits',
			'default' => array(),
			'dependencies' => array('feature_categories'),
		),
		'forum_category_selector_in_list' => array(
			'name' => tr('Include category selector in forum list'),
			'description' => tr("Include a drop list in the forum list to select the post's category."),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array('feature_categories'),
		),
	);
}
