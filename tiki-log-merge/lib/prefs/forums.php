<?php

function prefs_forums_list() {
	return array(
		'forums_ordering' => array(
			'name' => tra('Default ordering'),
			'type' => 'list',
			'options' => array(
				'created_asc' => tra('Creation Date (asc)'),
				'created_desc' => tra('Creation Date (desc)'),
				'threads_desc' => tra('Topics (desc)'),
				'comments_desc' => tra('Threads (desc)'),
				'lastPost_desc' => tra('Last post (desc)'),
				'hits_desc' => tra('Visits (desc)'),
				'name_desc' => tra('Name (desc)'),
				'name_asc' => tra('Name (asc)'),
			),
		),
	);
}
