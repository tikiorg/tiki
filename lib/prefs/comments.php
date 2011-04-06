<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_comments_list() {
	return array(
		'comments_notitle' => array(
			'name' => tra('Disable title for comments'),
			'description' => tra('Hide the title field on comments and their replies.'),
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
			'description' => tra('Allows users with permission tiki_p_vote_comments to vote comments.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'comments_archive' => array(
			'name' => tra('Archive comments'),
			'description' => tra('If a comment is archived, only admins can see it'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
