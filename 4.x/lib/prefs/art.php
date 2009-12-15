<?php

function prefs_art_list() {
	return array(
		'art_list_title' => array(
			'name' => tra('Title'),
			'type' => 'flag',
		),
		'art_list_title_len' => array(
			'name' => tra('Title length'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
		),
		'art_list_type' => array(
			'name' => tra('Type'),
			'type' => 'flag',
		),
		'art_list_topic' => array(
			'name' => tra('Topic'),
			'type' => 'flag',
		),
		'art_list_date' => array(
			'name' => tra('Publication date'),
			'type' => 'flag',
		),
		'art_list_expire' => array(
			'name' => tra('Expiration date'),
			'type' => 'flag',
		),
		'art_list_visible' => array(
			'name' => tra('Visible'),
			'type' => 'flag',
		),
		'art_list_lang' => array(
			'name' => tra('Language'),
			'type' => 'flag',
		),
		'art_list_author' => array(
			'name' => tra('Author'),
			'type' => 'flag',
		),
		'art_list_rating' => array(
			'name' => tra('Rating'),
			'type' => 'flag',
		),
		'art_list_reads' => array(
			'name' => tra('Reads'),
			'type' => 'flag',
		),
		'art_list_size' => array(
			'name' => tra('Size'),
			'type' => 'flag',
		),
		'art_list_img' => array(
			'name' => tra('Images'),
			'type' => 'flag',
		),
	);
}
