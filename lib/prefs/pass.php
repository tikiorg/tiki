<?php

function prefs_pass_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'pass_chr_num' => array(
			'name' => tra('Require characters and numerals'),
			'type' => '',
		),
		// Used in templates/tiki-admin-include-login.tpl
		'pass_due' => array(
			'name' => tra('Password expires after'),
			'type' => '',
		),
	);	
}
