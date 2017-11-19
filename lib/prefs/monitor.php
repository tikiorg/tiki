<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_monitor_list()
{
	return [
		'monitor_enabled' => [
			'name' => tra('Notifications'),
			'description' => tra('Allows users to control the notifications they receive based on content changes.'),
			'type' => 'flag',
			'default' => 'n',
			'help' => 'Notifications',
		],
		'monitor_digest' => [
			'name' => tra('Notification digests'),
			'description' => tra('Enable digest notifications (requires a cron job)'),
			'type' => 'flag',
			'default' => 'n',
			'help' => 'Notifications#Digests',
		],
		'monitor_individual_clear' => [
			'name' => tra('Clear individual notifications'),
			'description' => tra('Allow users to selectively clear notifications instead of simply having a clear-all button.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'monitor_count_refresh_interval' => [
			'name' => tra('Notification count refresh interval'),
			'description' => tr('Show unread notification count and refresh every [x] seconds.'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 0,
			'units' => tra('seconds'),
			'size' => 5,
			'hint' => tr('0 to disable, every refresh causes a hit on the server, try to leave this above 60 seconds.'),
		],
		'monitor_reply_email_pattern' => [
			'name' => tr('Notification reply-to email pattern'),
			'description' => tra('Email model to use for the notification email reply-to address.'),
			'type' => 'text',
			'filter' => 'email',
			'default' => '',
			'hint' => tr('noreply+PLACEHOLDER@example.com'),
			'dependencies' => ['feature_mailin'],
		],
	];
}
