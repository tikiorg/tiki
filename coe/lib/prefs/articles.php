<?php

function prefs_articles_list() {
	return array(
		'articles_feature_copyrights' => array(
			'name' => tra('Articles'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_articles',
			),
		),
	);
}
