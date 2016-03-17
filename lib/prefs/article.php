<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	$prefslib = TikiLib::lib('prefs');
	$advanced_columns = $prefslib->getExtraSortColumns();

	foreach ( $advanced_columns as $key => $label ) {
		$comment_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$comment_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

	return array(
		'article_comments_per_page' => array(
			'name' => tra('Default number per page'),
            'description' => tra('Sets the number of comments per page (default = 10)'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 10,
		),
		'article_comments_default_ordering' => array(
			'name' => tra('Default Ordering'),
            'description' => tra('sSets the default ordering filter for comments (default = points_desc)'),
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
		'article_remembers_creator' => array(
			'name' => tra('Article creator remains article owner.'),
			'description' => tra('Last article editor does not automatically become author (owner).'),
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
			'default' => "0,1,2,3,4,5",
		),
		'article_image_size_x' => array(
			'name' => tra('Default maximum width for custom article images'),
            'description' => tra('Sets the maximum width of article image'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, "0" for no maximum'),
			'default' => '0',
		),
		'article_image_size_y' => array(
			'name' => tra('Default maximum height for custom article images'),
            'description' => tra('Sets the maximum height of article images'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels, "0" for no maximum') ,
			'default' => '0',
		),
		'article_default_list_image_size_x' => array(
			'name' => tra('Default maximum width for custom article images in list mode (on View Articles)'),
            'description' => tra('Sets the default maximum width of custom article images in list mode (on View Articles page)'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels ("0" to default to the view mode maximum)'),
			'default' => '0',
		),
		'article_default_list_image_size_y' => array(
			'name' => tra('Default maximum height of custom article images in list mode (on View Articles page)'),
            'description' => tra('Sets the default maximum height of custom article images in list mode (on View Articles page)'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'int',
			'hint' => tra('Number of pixels ("0" to default to the view mode maximum)'),
			'default' => '0',
		),
		'article_custom_attributes' => array(
			'name' => tra('Custom attributes for article types'),
			'description' => tra('Enable additional custom fields for article types'),
			'type' => 'flag',
			'default' => 'y',
		),
		'article_sharethis_publisher' => array(
			'name' => tra('Your ShareThis publisher identifier (optional)'),
			'description' => tra('Set to define your ShareThis publisher identifier'),
			'type' => 'text',
			'size' => '40',
            'hint' => tra('record your ShareThis publisher ID'),
			'default' => '',
		),
		'article_related_articles' => array(
			'name' => tr('Related articles'),
			'description' => tr('Display a list of related articles at the bottom of an article page'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_freetags',
			),
		),
		'article_use_new_list_articles' => array(
			'name' => tr('Use New Articles'),
			'description' => tr('Uses the new article lists using CustomSearch rather than the DB'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('experimental'),
		),
	);
}
