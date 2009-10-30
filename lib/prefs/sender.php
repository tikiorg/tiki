<?php

function prefs_sender_list() {
	return array(
		'sender_email' => array(
			'name' => tra('Sender email'),
			'description' => tra('Email address that will be used as the sender for outgoing emails.'),
			'type' => 'text',
			'size' => 40,
		),
	);
}
