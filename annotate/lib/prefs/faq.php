<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_faq_list() {
	return array(
		'faq_comments_per_page' => array(
			'name' => tra('Default number of comments per page'),
			'type' => 'text',
			'size' => '5',
			'default' => 10,
		),
		'faq_comments_default_ordering' => array(
			'name' => tra('Comments default ordering'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
			'default' => 'points_desc',
		),
		'faq_prefix' => array(
			'name' => tra('Question and Answer prefix on Answers'),
			'type' => 'list',
			'options' => array(
				'none' => tra('None'),
				'QA' => tra('Q and A'),
				'question_id' => tra('Question ID'),
			),
			'default' => 'QA',
		),
	);
}
