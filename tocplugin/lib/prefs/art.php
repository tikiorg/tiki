<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_art_list()
{
	$article_sort_orders = array(
		'publishDate_desc' => tra('Newest first'),
	);

	$prefslib = TikiLib::lib('prefs');
	$advanced_columns = $prefslib->getExtraSortColumns();

	foreach ( $advanced_columns as $key => $label ) {
		$article_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$article_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

	return array(
		'art_sort_mode' => array(
			'name' => tra('Article ordering'),
			'description' => tra('Default sort mode for the articles on the list-articles page'),
			'type' => 'list',
			'options' => $article_sort_orders,
			'default' => 'publishDate_desc',
		),
		'art_home_title' => array(
			'name' => tra('Title of articles homepage'),
			'type' => 'list',
			'options' => array(
				'' => '',
				'topic' => tra('Topic'),
				'type' => tra('Type'),
				'articles' => tra('Articles'),
			),
			'default' => '',
		),
		'art_list_title' => array(
			'name' => tra('Title'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_title_len' => array(
			'name' => tra('Title length'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '50',
		),
		'art_list_type' => array(
			'name' => tra('Type'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_topic' => array(
			'name' => tra('Topic'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_date' => array(
			'name' => tra('Publication date'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_expire' => array(
			'name' => tra('Expiration date'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_visible' => array(
			'name' => tra('Visible'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_lang' => array(
			'name' => tra('Language'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_author' => array(
			'name' => tra('Author (owner)'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_authorName' => array(
			'name' => tra('Author name (as displayed)'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_rating' => array(
			'name' => tra('Author rating'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_usersRating' => array(
			'name' => tra('Users rating'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_reads' => array(
			'name' => tra('Reads'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_size' => array(
			'name' => tra('Size'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_img' => array(
			'name' => tra('Images'),
			'type' => 'flag',
			'default' => 'n',
		),
		'art_list_id' => array(
			'name' => tra('Id'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_list_ispublished' => array(
			'name' => tra('Is published'),
			'type' => 'flag',
			'default' => 'y',
		),
		'art_trailer_pos' => array(
			'name' => tra('Trailer position'),
			'description' => tra('Trailer position'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top'),
				'between' => tra('Between heading and body'),
			),
			'default' => 'top',
		),
		'art_header_text_pos' => array(
			'name' => tra('Header text position'),
			'description' => tra('Header text position'). tra('Requires a smaller image for list view'),
			'type' => 'list',
			'options' => array(
				'next' => tra('Next image'),
				'below' => tra('Below image'),
			),
			'default' => 'next'
		),

	);
}
