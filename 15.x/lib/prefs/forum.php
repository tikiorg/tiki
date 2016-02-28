<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_forum_list()
{
	return array(
		'forum_image_file_gallery' => array(
			'name' => tr('Forum image file gallery'),
			'description' => tr('File gallery used to store images for forums'),
			'type' => 'text',
			'default' => 0,
			'profile_reference' => 'file_gallery',
			'dependencies' => ['feature_file_galleries'],
		),
		'forum_comments_no_title_prefix' => array(
			'name' => tra("Do not start messages titles with 'Re: '"),
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
			'name' => tra('Display the thread configuration bar only when the number of posts exceeds'),
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
				'commentStyle_headers' => tra('Headers only'),
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
			'name' => tra("Don't display forum thread titles"),
			'description' => tra("Titles of posts usually don't change because they are a direct reply to the parent post. This feature turns off the display of titles in edit forms and forum display."),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_reply_forcetitle' => array(
			'name' => tra('Require reply to have a title'),
			'description' => tra('Present an empty title input form and require it to be filled in before the forum post is submitted.'),
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
			'profile_reference' => 'category',
		),
		'forum_category_selector_in_list' => array(
			'name' => tr('Include category selector in forum list'),
			'description' => tr("Include a dropdown selector in the forum list to choose a category for the post."),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array('feature_categories'),
		),
		'forum_inbound_mail_ignores_perms' => array(
			'name' => tr('Allow inbound email posts from anyone'),
			'description' => tr('Allow posts from non-users in forums using inbound posts from a specified email address.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'forum_inbound_mail_parse_html' => array(
			'name' => tr('Parse HTML in inbound email posts'),
			'description' => tr('Attempt to keep the formatting of HTML "rich text" emails if using WYSIWYG.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('experimental'),
			'warning' => tra('Experimental') . ' ' . tra('Has problems with some HTML emails, especially those with table-based layouts.'),
			'dependencies' => array('feature_wysiwyg'),
		),
		'forum_strip_wiki_syntax_outgoing' => array(
			'name' => tr('Strip wiki markup from outgoing forum emails'),
			'description' => tr('Converts outgoing emails from forum posts to plain text.'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array('feature_forum_parse'),
		),
	);
}
