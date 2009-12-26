<?php

function prefs_pam_list() {
	return array(
		'pam_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => 'flag',
		),
		'pam_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
		),
		'pam_service' => array(
			'name' => tra('PAM service'),
			'type' => 'text',
			'size' => 20,
			'hint' => tra('Currently unused'),
		),
	);	
}
