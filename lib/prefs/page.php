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
		'page_n_times_in_a_structure' => array(
			'name' => tra('A page can occur multiple times in a structure'),
			'type' => 'flag',
		),
	);
}
