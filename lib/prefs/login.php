<?php

function prefs_login_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'login_is_email' => array(
			'name' => tra('Use email as username'),
			'type' => '',
		),
	);	
}
