<?php

function prefs_contact_list() {
	return array (
		'contact_anon' => array(
			'name' => tra('Allow anonymous visitors to use the "Contact Us" feature.'),
			'description' => tra('Allow anonymous visitors to use the "Contact Us" feature.'),
			'type' => 'flag',
			'help' => 'Contact+Us',
			'dependencies' => array(
				'feature_contact',
			),
		),
		'contact_user' => array(
			'name' => tra('Contact user'),
			'description' => tra('Contact user'),
			'type' => 'text',
			'size' => 40,
			'dependencies' => array(
				'feature_contact',
			),
		),
	);
}
