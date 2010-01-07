<?php

function prefs_faq_list() {
	return array(
		'faq_comments_per_page' => array(
			'name' => tra('Default number of comments per page'),
			'type' => 'text',
			'size' => '5',
		),
		'faq_comments_default_ordering' => array(
			'name' => tra('Comments default ordering'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
		),
		'faq_prefix' => array(
			'name' => tra('Question and Answer prefix on Answers'),
			'type' => 'list',
			'options' => array(
				'none' => tra('None'),
				'QA' => tra('Q and A'),
				'question_id' => tra('Question ID'),
			),
		),
	);
}
