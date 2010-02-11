<?php

function prefs_javascript_list() {
	return array(
		'javascript_cdn' => array(
			'name' => tra('Use CDN for Javascript'),
			'description' => tra('Obtain jQuery and jQuery UI libraries through a content delivery network.'),
			'type' => 'list',
			'options' => array(
				'none' => tra('None'),
				'google' => tra('Google'),
			),
		),
	);
}

