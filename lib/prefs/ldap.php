<?php

function prefs_ldap_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'ldap_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => '',
		),
		// Used in templates/tiki-admin-include-login.tpl
		'ldap_create_user_ldap' => array(
			'name' => tra('Create user if not in LDAP'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'ldap_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => '',
		),
	);	
}
