<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_forum_list() {
	return array(
		'forum_comments_no_title_prefix' => array(
			'name' => tra("Do not prefix messages titles by 'Re: '"),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_match_regex' => array(
			'name' => tra('Uploaded filenames must match regex'),
			'type' => 'text',
			'size' => '20',
			'default' => '',
		),
		'forum_thread_defaults_by_forum' => array(
			'name' => tra('Manage thread defaults per-forum'),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_thread_user_settings' => array(
			'name' => tra('Display thread configuration bar'),
			'type' => 'flag',
			'hint' => tra('Allows users to override the defaults'),
			'default' => 'y',
		),
		'forum_thread_user_settings_threshold' => array(
                        'name' => tra('Display thread configuration bar only when number of posts exceed'),
                        'type' => 'text',
			'size' => '5',
			'filter' => 'digit',
			'default' => 10,
                ),
		'forum_thread_user_settings_keep' => array(
			'name' => tra('Keep settings for all forums during the user session'),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_comments_per_page' => array(
			'name' => tra('Default number per page'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digit',
			'default' => 20,
		),
		'forum_thread_style' => array(
			'name' => tra('Default style'),
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
			'type' => 'flag',
			'default' =>  'n',
		),
		'forum_list_posts' => array(
			'name' => tra('Posts'),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_ppd' => array(
			'name' => tra('Posts per day') . ' (PPD)',
			'type' => 'flag',
			'default' =>  'n',
		),
		'forum_list_lastpost' => array(
			'name' => tra('Last post'),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_visits' => array(
			'name' => tra('Visits'),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_desc' => array(
			'name' => tra('Description'),
			'type' => 'flag',
			'default' =>  'y',
		),
		'forum_list_description_len' => array(
			'name' => tra('Description length'),
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
	);
}
