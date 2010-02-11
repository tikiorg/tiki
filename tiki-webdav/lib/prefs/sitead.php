<?php

function prefs_sitead_list() {
	return array(
		'sitead_publish' => array(
			'name' => tra('Publish'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_sitead',
			),
		),	
	);
}
