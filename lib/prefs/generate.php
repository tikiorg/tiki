<?php

function prefs_generate_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'generate_password' => array(
			'name' => tra('Include &quot;Generate Password&quot; option on registration form'),
			'type' => 'flag',
		),
	);	
}
