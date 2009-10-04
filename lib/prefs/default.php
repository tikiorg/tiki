<?php

function prefs_default_list() {
	return array(
		'default_mail_charset' => array(
			'name' => tra('Default charset for sending mail'),
			'description' => tra('Default charset for sending mail'),
			'type' => 'list',
			'options' => array(
				'utf-8' => tra('utf-8'),
				'iso-8859-1' => tra('iso-8859-1'),
			),
		),
		'default_map' => array(
			'name' => tra('default mapfile'),
			'type' => 'text',
			'size' => '50',
		),
	);
}
