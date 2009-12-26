<?php

function prefs_pam_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'pam_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => '',
		),
		// Used in templates/tiki-admin-include-login.tpl
		'pam_service' => array(
			'name' => tra('PAM service:'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-login.tpl
		'pam_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => '',
		),
	);	
}
	
