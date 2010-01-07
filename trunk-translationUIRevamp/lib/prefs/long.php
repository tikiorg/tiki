<?php

function prefs_long_list() {
	return array(
		'long_date_format' => array(
			'name' => tra('Long date format'),
			'type' => 'text',
			'size' => '30',
		),
		'long_time_format' => array(
			'name' => tra('Long time format'),
			'type' => 'text',
			'size' => '30',
		),
	);	
}
