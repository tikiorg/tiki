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
		'wikiHomePage' => array(
			'name' => tra('Home page'),
			'description' => tra('Landing page used for the wiki when no page is specified. The page will be created if it does not exist.'),
			'type' => 'text',
			'size' => 20,
		),
		'useGroupHome' => array(
			'name' => tra('Use group homepages'),
			'description' => tra('Use group homepages'),
			'type' => 'flag',
			'help' => 'Group',
		),
		'limitedGoGroupHome' => array(
			'name' => tra('Go to group homepage only if login from default homepage'),
			'description' => tra('Go to group homepage only if login from default homepage'),
			'type' => 'flag',
			'dependencies' => array(
				'useGroupHome',
			),
		),
	);
}
