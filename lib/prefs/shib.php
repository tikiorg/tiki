<?php

function prefs_shib_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'shib_affiliation' => array(
			'name' => tra('Valid affiliations:'),
			'type' => '',
		),
		// Used in templates/tiki-admin-include-login.tpl
		'shib_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'shib_group' => array(
			'name' => tra('Default group:'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'shib_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'shib_usegroup' => array(
			'name' => tra('Create with default group'),
			'type' => '',
		),
	);	
}
