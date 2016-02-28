<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_comments_list()
{
	return array(
		'comments_notitle' => array(
			'name' => tra('Disable comment titles'),
			'description' => tra('Don\'t display the title input field on comments and their replies.'),
			'type' => 'flag',
			'default' => 'y',
		),
		'comments_field_email' => array(
			'name' => tra('Email field'),
			'description' => tra('Email field for comments (only for anonymous users).'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_field_website' => array(
			'name' => tra('Website field'),
			'description' => tra('Website field for comments (only for anonymous users).'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_vote' => array(
			'name' => tra('Use vote system for comments'),
			'description' => tra('Allow users with permission (tiki_p_vote_comments) to vote on comments.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_archive' => array(
			'name' => tra('Archive comments'),
			'description' => tra('If a comment is archived, only admins can see it'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_akismet_filter' => array(
			'name' => tra('Use Akismet to filter comments'),
			'description' => tra('Prevent comment spam by using the Akismet service to determine if the comment is spam. If comment moderation is enabled, Akismet will indicate if the comment is to be moderated or not. If there is no comment moderation, the comment will be rejected if considered to be spam.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
			'keywords' => 'askimet', // Let an admin find the preference even if his query has this common typo
		),
		'comments_akismet_apikey' => array(
			'name' => tra('Akismet API Key'),
			'description' => tra('Key required for the Akismet comment spam prevention.'),
			'hint' => tr('Obtain this key by registering your site at [%0]', 'http://akismet.com'),
			'type' => 'text',
			'filter' => 'word',
			'tags' => array('advanced'),
			'default' => '',
			'keywords' => 'askimet',	
		),
		'comments_akismet_check_users' => array(
			'name' => tr('Filter spam for registered users'),
			'description' => tr('Activate spam filtering for registered users as well. Useful if your site allows anyone to register without screening.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
			'keywords' => array('askimet'),			
		),
		'comments_allow_correction' => array(
			'name' => tr('Allow comments to be edited by their author'),
			'description' => tr('Allow a comment to be modified by its author for a 30-minute period after posting it, for clarifications, correction of errors, etc.'),
			'type' => 'flag',
			'default' => 'y',
			'tags' => array('advanced'),
		),
	);
}
