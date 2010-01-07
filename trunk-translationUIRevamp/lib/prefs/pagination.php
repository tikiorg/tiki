<?php

function prefs_pagination_list() {
	return array(
		'pagination_firstlast' => array(
			'name' => tra("Display 'First' and 'Last' links"),
			'type' => 'flag',
		),
		'pagination_fastmove_links' => array(
			'name' => tra('Display fast move links (by 10 percent of the total number of pages) '),
			'type' => 'flag',
		),
		'pagination_hide_if_one_page' => array(
			'name' => tra('Hide pagination when there is only one page'),
			'type' => 'flag',
		),
		'pagination_icons' => array(
			'name' => tra('Use Icons'),
			'type' => 'flag',
		),
	);	
}
