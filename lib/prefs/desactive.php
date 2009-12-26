<?php

function prefs_desactive_list() {
	return array(
		'desactive_login_autocomplete' => array(
			'name' => tra("Disable browser's autocomplete feature for username and password fields"),
			'type' => 'flag',
		),
	);	
}
