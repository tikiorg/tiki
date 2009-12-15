<?php

function prefs_mail_list() {
	return array(
		'mail_crlf' => array(
			'name' => tra('Mail end of line'),
			'description' => tra('Mail end of line'),
			'help' => '',
			'type' => 'list',
			'options' => array(
				'CRLF' => tra('CRLF (standard)'),
				'LF' => tra('LF (some Unix MTA)'),
			),
		),
	);
}
