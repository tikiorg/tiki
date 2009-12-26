<?php

function prefs_cookie_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'cookie_path' => array(
			'name' => tra('Path:'),
			'type' => 'text',
			'perspective' => false,
		),
		// Used in templates/tiki-admin-include-login.tpl
		'cookie_domain' => array(
			'name' => tra('Domain:'),
			'type' => 'text',
			'perspective' => false,
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'cookie_name' => array(
			'name' => tra('Cookie name:'),
			'type' => 'text',
			'perspective' => false,
		),
	);
}
	
