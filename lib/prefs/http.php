<?php

function prefs_http_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'http_port' => array(
			'name' => tra('HTTP port:'),
			'type' => '',
		),
	);	
}
