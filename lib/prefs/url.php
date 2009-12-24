<?php

function prefs_url_list() {
	return array(
		'url_after_validation' => array(
			'name' => tra('Url a user is redirected to after account validation'),
			'hint' => tra('Default').': tiki-information.php?msg='.tra('Account validated successfully.'),
			'type' => 'text',
			'dependencies' => array(
				'allowRegister',
			),
		),
	);
}
