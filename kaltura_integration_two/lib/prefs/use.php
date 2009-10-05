<?php

function prefs_use_list() {
	return array (
		'use_load_threshold' => array(
			'name' => tra('Close site when server load is above the threshold  (except for those with permission)'),
			'description' => tra('Close site when server load is above the threshold  (except for those with permission)'),
			'type' => 'flag',
		),
		'use_proxy' => array(
			'name' => tra('Use proxy'),
			'description' => tra('Use proxy'),
			'type' => 'flag',
		),
	);
}
