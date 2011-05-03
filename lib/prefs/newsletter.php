<?php

function prefs_newsletter_list()
{
	return array(
		'newsletter_throttle' => array(
			'name' => tra('Throttle newsletter send rate'),
			'description' => tra('Pause for a given amount of seconds before each batch to avoid overloading the mail server.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'newsletter_pause_length' => array(
			'name' => tra('Newsletter pause length'),
			'description' => tra('Amount of seconds on wait before each batch'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => 60,
		),
		'newsletter_batch_size' => array(
			'name' => tra('Newsletter batch size'),
			'description' => tra('Amount of emails to send in each batch.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => 5,
		),
	);
}
