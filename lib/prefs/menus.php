<?php

function prefs_menus_list() {
	return array(
		'menus_items_icons' => array(
			'name' => tra('Allow users to define icons for menus entries'),
			'type' => 'flag',
		),
		'menus_items_icons_path' => array(
			'name' => tra('Default path for the icons'),
			'type' => 'text',
		),
	);
}
