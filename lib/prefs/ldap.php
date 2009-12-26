<?php

function prefs_ldap_list() {
	return array(
		'ldap_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => 'flag',
		),
		'ldap_create_user_ldap' => array(
			'name' => tra('Create user if not in LDAP'),
			'type' => 'flag',
		),
		'ldap_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
		),
	);	
}
