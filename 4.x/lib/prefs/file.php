<?php

function prefs_file_list() {
	return array(
		'file_galleries_comments_per_page' => array(
			'name' => tra('Default number per page'),
			'type' => 'text',
			'size' => '5',
		),
		'file_galleries_comments_default_ordering' => array(
			'name' => tra('Default number per page'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
		),
	);
}