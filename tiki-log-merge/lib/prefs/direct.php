<?php

function prefs_direct_list() {
	return array(
		'direct_pagination' => array(
			'name' => tra('Use direct pagination links'),
			'type' => 'flag',
		),
		'direct_pagination_max_middle_links' => array(
			'name' => 'Max. number of links around the current item',
			'type' => 'text',
			'size' => '4',
		),
		'direct_pagination_max_ending_links' => array(
			'name' => tra('Max. number of links after the first or before the last item'),
			'type' => 'text',
			'size' => '4',
		),
	);	
}
