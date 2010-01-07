<?php

function prefs_messu_list() {
	return array(
		'messu_mailbox_size' => array(
			'name' => tra('Maximum mailbox size (messages, 0=unlimited)'),
			'description' => tra('Maximum mailbox size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
		),
		'messu_archive_size' => array(
			'name' => tra('Maximum mail archive size (messages, 0=unlimited)'),
			'description' => tra('Maximum mail archive size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
		),
		'messu_sent_size' => array(
			'name' => tra('Maximum sent box size (messages, 0=unlimited)'),
			'description' => tra('Maximum sent box size (messages, 0=unlimited)'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'dependencies' => array(
				'feature_messages',
			),
		),
	);
}
