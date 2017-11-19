<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_messu_list()
{
	return [
		'messu_mailbox_size' => [
			'name' => tra('Maximum mailbox size'),
			'description' => tra('Maximum number of messages allowed in the mailbox.'),
			'type' => 'text',
			'size' => '10',
			'hint' => tr('0 for unlimited'),
			'filter' => 'digits',
			'units' => tra('messages'),
			'dependencies' => [
				'feature_messages',
			],
			'default' => '0',
		],
		'messu_archive_size' => [
			'name' => tra('Maximum mail archive size'),
			'description' => tra('Maximum number of archive messages allowed.'),
			'type' => 'text',
			'size' => '10',
			'hint' => tr('0 for unlimited'),
			'filter' => 'digits',
			'units' => tra('messages'),
			'dependencies' => [
				'feature_messages',
			],
			'default' => '200',
		],
		'messu_sent_size' => [
			'name' => tra('Maximum sent box size'),
			'description' => tra('Maximum number of sent messages allowed in the mailbox.'),
			'type' => 'text',
			'size' => '10',
			'hint' => tr('0 for unlimited'),
			'filter' => 'digits',
			'units' => tra('messages'),
			'dependencies' => [
				'feature_messages',
			],
			'default' => '200',
		],
		'messu_truncate_internal_message' => [
			'name' => tra('Truncate internal message notification'),
			'description' => tra('Number of characters to show in the message notification sent through email, with a link to read the full internal message.'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'units' => tra('characters'),
			'dependencies' => [
				'feature_messages',
			],
			'default' => '2500',
		],
	];
}
