<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_share_list()
{
	return [
		'share_display_links' => [
			'name' => tra('Display links on the share page'),
			'type' => 'flag',
			'default' => 'y',
		],
		'share_token_notification' => [
			'name' => tra('Token notification'),
			'description' => tra('Users can subscribe to the notifications of consultation of the token'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => [
				'auth_token_access'
			],
			'default' => 'y',
		],
		'share_contact_add_non_existant_contact' => [
			'name'	=> tra('Add contact'),
			'description' => tra('If not already in the list, recipients are added to the list of contacts'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => [
				'feature_contacts',
			],
			'default' => 'n',
		],
		'share_display_name_and_email' => [
			'name' => tra('Display name and email'),
			'description' => tra('If the user is connected, the name and email will display in the page'),
			'type' => 'flag',
			'default' => 'y',
		],
		'share_can_choose_how_much_time_access' => [
			'name' => tra('Number of times accessed'),
			'description' => tra('User can choose how many times the share page can be consulted'),
			'type' => 'flag',
			'dependencies' => [
				'auth_token_access',
			],
			'default' => 'n',
		],
		'share_max_access_time' => [
			'name' => tra('Maximum number of times accessed'),
			'description' => tra('Maximum number of times that the shared page can be consulted'),
			'type' => 'text',
			'units' => tra('page hits'),
			'default' => '-1',
		],

	];
}
