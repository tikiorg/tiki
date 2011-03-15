<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_article_list() {
	$comment_sort_orders = array(
		'commentDate_desc' => tra('Newest first'),
		'commentDate_asc' => tra('Oldest first'),
		'points_desc' => tra('Points'),
	);

	global $prefslib;
	$advanced_columns = $prefslib->getExtraSortColumns();

	foreach( $advanced_columns as $key => $label ) {
		$comment_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$comment_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

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
			'options' => $comment_sort_orders,
		),
		'article_paginate' => array(
			'name' => tra('Paginate articles'),
			'description' => tra('Divide articles into multiple pages with pagebreak markers.'),
			'type' => 'flag',
		),
		'article_user_rating' => array(
			'name' => tra('User ratings on articles'),
			'description' => tra('Allows users to rate the articles.'),
			'type' => 'flag',
		),
		'article_user_rating_options' => array(
			'name' => tra('Article rating options'),
			'description' => tra('List of options available for the rating of articles.'),
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
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
		'article_custom_attributes' => array(
			'name' => tra('Custom attributes for article types'),
			'description' => tra('Allow additional custom fields for article types'),
			'type' => 'flag',
		),
		'article_sharethis_publisher' => array(
			'name' => tra('Your ShareThis publisher identifier (optional)'),
			'type' => 'text',
			'size' => '40',
		),

	);
}
