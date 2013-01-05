<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_article_list()
{
	$comment_sort_orders = array(
		'commentDate_desc' => tra('Newest first'),
		'commentDate_asc' => tra('Oldest first'),
		'points_desc' => tra('Points'),
	);

	global $prefslib;
	$advanced_columns = $prefslib->getExtraSortColumns();

	foreach ( $advanced_columns as $key => $label ) {
		$comment_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$comment_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

	return array(
		'article_comments_per_page' => array(
			'name' => tra('Default number per page'),
            'description' => tra('set the number of comments per page (default = 10)'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 10,
		),
		'article_comments_default_ordering' => array(
			'name' => tra('Default Ordering'),
            'description' => tra('sets the default ordering filter for comments (default = points_desc)'),
			'type' => 'list',
			'options' => $comment_sort_orders,
			'default' => 'points_desc',
		),
		'article_paginate' => array(
			'name' => tra('Paginate articles'),
			'description' => tra('Divide articles into multiple pages with pagebreak markers.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'article_user_rating' => array(
			'name' => tra('User ratings on articles'),
			'description' => tra('Allows users to rate the articles.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'article_user_rating_options' => array(
			'name' => tra('Article rating options'),
			'description' => tra('List of options available for the rating of articles.'),
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
			'default' => range(1, 5),
		),
		'article_image_size_x' => array(
			'name' => tra('Default maximum width for custom article images'),
            'description' => tra('sets the maximum width for article images'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, 0 for no maximum'),
			'default' => '0',
		),
		'article_image_size_y' => array(
			'name' => tra('Default maximum height for custom article images'),
            'description' => tra('sets the maximum height for article images'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, 0 for no maximum') ,
			'default' => '0',
		),
		'article_default_list_image_size_x' => array(
			'name' => tra('Default maximum width for custom article images in list mode (on View Articles)'),
            'description' => tra('sets the default maximum width for custom article images in list mode (on View Articles)'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, 0 to fallback to the view mode maximum'),
			'default' => '0',
		),
		'article_default_list_image_size_y' => array(
			'name' => tra('Default maximum height for custom article images in list mode (on View Articles)'),
            'description' => tra('sets the default maximum height for custom article images in list mode (on View Articles)'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, 0 to fallback to the view mode maximum'),
			'default' => '0',
		),
		'article_custom_attributes' => array(
			'name' => tra('Custom attributes for article types'),
			'description' => tra('Allow additional custom fields for article types'),
			'type' => 'flag',
			'default' => 'y',
		),
		'article_sharethis_publisher' => array(
			'name' => tra('Your ShareThis publisher identifier (optional)'),
            'description' => tra('set to define your ShareThis publisher identifier'),
			'type' => 'text',
			'size' => '40',
            'hint' => tra('record your ShareThis publisher ID'),
			'default' => '',
		),
		'article_related_articles' => array(
			'name' => tr('Related articles'),
			'description' => tr('Display a list of related articles at the bottom of the article page'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_freetags',
			),
		),
	);
}
