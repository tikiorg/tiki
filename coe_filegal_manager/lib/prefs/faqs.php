<?php

function prefs_faqs_list() {
	return array(
		'faqs_feature_copyrights' => array(
			'name' => tra('Faqs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_faqs',
			),
		),
	);
}
