<?php

function prefs_page_list() {
	return array(
		'page_bar_position' => array(
			'name' => tra('Wiki buttons'),
			'description' => tra('Page description, icons, backlinks, ...'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top '),
				'bottom' => tra('Bottom'),
				'none' => tra('Neither'),
			),
		),
	);
}
