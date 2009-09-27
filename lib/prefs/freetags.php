<?php

function prefs_freetags_list() {
	return array (
			'freetags_multilingual' => array(
			'name' => tra('Multilingual tags'),
			'description' => tra('Permits translation management of tags'),
			'help' => 'Tags',
			'type' => 'flag',
			'dependencies' => array(
				'feature_multilingual',
				'feature_freetags',
			),
		),
	);
}
