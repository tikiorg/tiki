<?php

function prefs_error_list() {
	return array(
		'error_reporting_adminonly' => array(
			'name' => tra('PHP errors visible to admin only'),
			'description' => tra('For development purposes, it\'s better to display errors for all users. However, in production settings, they should only be displayed to administrators.'),
			'type' => 'flag',
		),
		'error_reporting_level' => array(
			'name' => tra('PHP Error reporting level'),
			'description' => tra('Level from which errors should be reported.'),
			'type' => 'list',
			'options' => array(
				0 => tra('No error reporting'),
				2047 => tra('Report all PHP errors except strict'),
				-1 => tra('Report all PHP errors'),
				2039 => tra('Report all PHP errors except notices'),
			),
		),
	);
}
