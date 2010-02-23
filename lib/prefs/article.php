<?php

function prefs_article_list() {
	return array(
		'article_comments_per_page' => array(
			'name' => tra('Default number per page'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
		),
		'article_comments_default_ordering' => array(
			'name' => tra('Default Ordering'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),

		),
		'article_image_size_x' => array(
			'name' => tra('Default article image width'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('0 for original image size'),
		),
		'article_image_size_y' => array(
			'name' => tra('Default article image height'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('0 for original image size.') ,
		),
	);
}
