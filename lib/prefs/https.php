<?php

function prefs_https_list() {
	return array(
		'https_external_links_for_users' => array(
			'name' => tra('Use HTTPS when building user-specific links'),
			'description' => tra('When building notification emails, RSS feeds or other externally available links, use HTTPS when the content applies to a specific user. HTTPS must be configured on the server.'),
			'type' => 'flag',
		),
		// Used in templates/tiki-admin-include-login.tpl
		'https_login' => array(
			'name' => tra('Use HTTPS login:'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'https_port' => array(
			'name' => tra('HTTPS port:'),
			'type' => '',
		),
	
	);
}
