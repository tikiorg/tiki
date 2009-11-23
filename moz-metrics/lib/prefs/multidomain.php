<?php

function prefs_multidomain_list() {
	return array(
		'multidomain_active' => array(
			'name' => tra('Multi-domain'),
			'description' => tra('Allows to map domain names to perspectives and simulate multiple domains hosted on the same instance.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
		),
		'multidomain_config' => array(
			'name' => tra('Multi-domain Configuration'),
			'description' => tra('Comma-separated values mapping the domain name to the perspective ID.'),
			'type' => 'textarea',
			'size' => 10,
			'hint' => tra('One domain per line. Comma separated with perspective ID.'),
		),
	);
}
