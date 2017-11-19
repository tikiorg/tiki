<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_faq_list()
{
	return [
		'faq_comments_per_page' => [
			'name' => tra('Default number of comments per page'),
			'description' => tra('number of comments to show per page'),
			'type' => 'text',
			'units' => tra('comments'),
			'size' => '5',
			'default' => 10,
		],
		'faq_comments_default_ordering' => [
			'name' => tra('Default order of comments'),
			'description' => tra('Default order of listed comments'),
			'type' => 'list',
			'options' => [
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			],
			'default' => 'points_desc',
		],
		'faq_prefix' => [
			'name' => tra('Prefix for answers'),
			'description' => tra('Question and answer prefix'),
			'type' => 'list',
			'options' => [
				'none' => tra('None'),
				'QA' => tra('Q and A'),
				'question_id' => tra('Question ID'),
			],
			'default' => 'QA',
		],
		'faq_feature_copyrights' => [
			'name' => tra('FAQ copyright'),
			'description' => tra(''),
			'type' => 'flag',
			'dependencies' => [
				'feature_faqs',
			],
			'default' => 'n',
		],
	];
}
