<?php

function prefs_max_list() {
	return array(
		'max_username_length' => array(
			'name' => tra('Maximum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'max_rss_articles' => array(
			'name' => tra('Maximum number of items to display'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);	
}
