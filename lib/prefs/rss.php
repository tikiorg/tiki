<?php

function prefs_rss_list() {
	return array(
		'rss_basic_auth' => array(
			'name' => tra('RSS basic Authentication '),
			'description' => tra('Propose basic http authentication if the user has no permission to see the feed'),
			'type' => 'flag',
			),
		'rss_articles' => array(
			'name' => tra('Articles'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_articles',
			),
		),
	);
}

