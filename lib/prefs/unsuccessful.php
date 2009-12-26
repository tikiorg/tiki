<?php

function prefs_unsuccessful_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'unsuccessful_logins' => array(
			'name' => tra('Re-validate user by email after'),
			'type' => '',
		),
	);	
}
