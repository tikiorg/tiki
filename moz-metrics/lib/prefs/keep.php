<?php

function prefs_keep_list() {
	return array(
		'keep_versions' => array(
			'name' => tra('Never delete versions younger than'),
			'type' => 'text',
			'size' => '5',
			'shorthint' => tra('days'),
		),
	);	
}
