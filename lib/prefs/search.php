<?php

function prefs_search_list() {
	return array (
		'search_parsed_snippet' => array(
			'name' => tra('Parse the results'),
			'hint' => tra('May impact performance'),
			'type' => 'flag',
		),
		'search_default_where' => array(
			'name' => tra('Where by default the search boxes'),
			'description' => tra('When object filter is not on, limit to search boxes to search one type of object'),
			'type' => 'list',
			'options' => array(
				'' => tra('Entire site'),
				'wikis' => tra('Wiki Pages'),
				'trackers' => tra('Trackers'),
			),
		),
	);
}
