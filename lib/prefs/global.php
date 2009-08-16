<?php

function prefs_global_list() {
	return array(
		'browsertitle' => array(
			'name' => tra('Browser title'),
			'description' => tra('Label visible in the browser\'s title bar on all pages. Also appears in search engines.'),
			'type' => 'text',
		),
		'validateUsers' => array(
			'name' => tra('Validate new user registrations by email'),
			'description' => tra('Upon registration, the new user will receive an email containing a link to confirm validity.'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
		),
	);
}

?>
