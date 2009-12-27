<?php

function prefs_use_list() {
	return array (
		'use_load_threshold' => array(
			'name' => tra('Close site when server load is above the threshold  (except for those with permission)'),
			'description' => tra('Close site when server load is above the threshold  (except for those with permission)'),
			'type' => 'flag',
			'perspective' => false,
		),
		'use_proxy' => array(
			'name' => tra('Use proxy'),
			'description' => tra('Use proxy'),
			'type' => 'flag',
			'perspective' => false,
		),
		'use_context_menu_icon' => array(
			'name' => tra('Use context menus for actions (icons)'),
			'type' => 'flag',
		),
		'use_context_menu_text' => array(
			'name' => tra('Use context menus for actions (text)'),
			'type' => 'flag',
		),
	);
}
