<?php

function prefs_category_list() {
	return array(
		'category_jail' => array(
			'name' => tra('Category Jail'),
			'description' => tra('Limits the visibility of objects to those in these categories. Used mainly for creating workspaces from perspectives.'),
			'separator' => ',',
			'type' => 'text',
			'filter' => 'int',
		),
	);
}

