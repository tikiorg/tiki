<?php

function prefs_desc_list() {
	return array(
		'desc_rss_articles' => array(
			'name' => tra('Article RSS Description'),
			'description' => tra('Description to be published as part of the RSS feed for articles.'),
			'type' => 'textarea',
			'size' => 2,
		),
	);
}
