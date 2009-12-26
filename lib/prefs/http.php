<?php

function prefs_http_list() {
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);	
}
