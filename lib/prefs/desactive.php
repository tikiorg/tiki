<?php

function prefs_desactive_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'desactive_login_autocomplete' => array(
			'name' => tra("Disable browser's autocomplete feature for username and password fields"),
			'type' => '',
		),
	);	
}
