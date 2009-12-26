<?php

function prefs_generate_list() {
	return array(
		'generate_password' => array(
			'name' => tra('Include "Generate Password" option on registration form'),
			'type' => 'flag',
		),
	);	
}
