<?php

function prefs_short_list() {
	return array(
		'short_date_format' => array(
			'name' => tra('Short date format'),
			'type' => 'text',
			'size' => '30',
		),
		'short_time_format' => array(
			'name' => tra('Short time format'),
			'type' => 'text',
			'size' => '30',
		),
	);
}
