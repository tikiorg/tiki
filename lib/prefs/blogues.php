<?php

function prefs_blogues_list() {
	return array(
		'blogues_feature_copyrights' => array(
			'name' => tra('Blogues'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
		),
	);
}
