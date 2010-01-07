<?php

function prefs_validator_list() {
	return array(
		'validator_emails' => array(
			'name' => tra('Validator emails (separated by comma) if different than the sender email'),
			'type' => 'text',
			'size' => 20,
		),
	);	
}
