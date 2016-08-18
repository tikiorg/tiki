<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_newsletter_list()
{
	return array(
		'newsletter_throttle' => array(
			'name' => tra('Throttle newsletter send rate'),
			'description' => tra('Pause for a given amount of seconds before each batch to avoid overloading the mail server.'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_newsletters',
			),
		),
		'newsletter_pause_length' => array(
			'name' => tra('Newsletter pause length'),
			'description' => tra('Number of seconds delay before each batch'),
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
		'newsletter_external_client' => array(
			'name' => tra('Allow sending newsletters through external clients'),
			'description' => tra('Generate mailto links using the recipients as the BCC list.'),
			'type' => 'flag',
			'default' => 'n',
			'warning' => tra('This will expose the list if email addresses to all users allowed to send newsletters.'),
			'dependencies' => array(
				'feature_newsletters',
			),
		),
	);
}
