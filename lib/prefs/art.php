<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_art_list()
{
	$article_sort_orders = [
		'publishDate_desc' => tra('Newest first'),
	];

	$prefslib = TikiLib::lib('prefs');
	$advanced_columns = $prefslib->getExtraSortColumns();

	foreach ($advanced_columns as $key => $label) {
		$article_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$article_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

	return [
		'art_sort_mode' => [
			'name' => tra('Article order'),
			'description' => tra('Default sort mode for the articles on the list-articles page'),
			'type' => 'list',
			'options' => $article_sort_orders,
			'default' => 'publishDate_desc',
		],
		'art_home_title' => [
			'name' => tra('Title of articles homepage'),
			'type' => 'list',
			'options' => [
				'' => '',
				'topic' => tra('Topic'),
				'type' => tra('Type'),
				'articles' => tra('Articles'),
			],
			'default' => '',
		],
		'art_list_title' => [
			'name' => tra('Title'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_title_len' => [
			'name' => tra('Title length'),
			'units' => tra('characters'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '50',
		],
		'art_list_type' => [
			'name' => tra('Type'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_topic' => [
			'name' => tra('Topic'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_date' => [
			'name' => tra('Publication date'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_expire' => [
			'name' => tra('Expiration date'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_visible' => [
			'name' => tra('Visible'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_lang' => [
			'name' => tra('Language'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_author' => [
			'name' => tra('Author (owner)'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_authorName' => [
			'name' => tra('Author name (as displayed)'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_rating' => [
			'name' => tra('Author rating'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_usersRating' => [
			'name' => tra('Users rating'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_reads' => [
			'name' => tra('Reads'),
			'type' => 'flag',
			'default' => 'y',
			'dependencies' => [
				'feature_stats',
			],
		],
		'art_list_size' => [
			'name' => tra('Size'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_img' => [
			'name' => tra('Images'),
			'type' => 'flag',
			'default' => 'n',
		],
		'art_list_id' => [
			'name' => tra('Id'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_list_ispublished' => [
			'name' => tra('Is published'),
			'type' => 'flag',
			'default' => 'y',
		],
		'art_trailer_pos' => [
			'name' => tra('Trailer position'),
			'description' => tra('Trailer position'),
			'type' => 'list',
			'options' => [
				'top' => tra('Top'),
				'between' => tra('Between heading and body'),
			],
			'default' => 'top',
		],
		'art_header_text_pos' => [
			'name' => tra('Header text position'),
			'description' => tra('Header text position') . tra('Requires a smaller image for list view'),
			'type' => 'list',
			'options' => [
				'next' => tra('Next image'),
				'below' => tra('Below image'),
			],
			'default' => 'next'
		],

	];
}
