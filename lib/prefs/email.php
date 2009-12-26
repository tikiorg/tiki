<?php

function prefs_email_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'email_due' => array(
			'name' => tra('Re-validate user by email after'),
			'type' => '',
		),
	);
}
