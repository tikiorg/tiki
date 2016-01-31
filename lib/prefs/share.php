<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_share_list()
{
	return array(
		'share_display_links' => array(
			'name' => tra('Display links on the share page'),
			'type' => 'flag',
			'default' => 'y',
		),
		'share_token_notification' => array(
			'name' => tra('Token notification'),
			'description' => tra('Users can subscribe to the notifications of consultation of the token'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'auth_token_access'
			),
			'default' => 'y',
		),
		'share_contact_add_non_existant_contact' => array(
			'name'	=> tra('Add contact'),
			'description' => tra('If not already in the list, recipients are added to the list of contacts'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'feature_contacts',
			),
			'default' => 'n',
		),
		'share_display_name_and_email' => array(
			'name' => tra('Display name and email'),
			'description' => tra('If the user is connected, the name and email will display in the page'),
			'type' => 'flag',
			'default' => 'y',
		),
		'share_can_choose_how_much_time_access' => array(
			'name' => tra('Number of times accessed'),
			'description' => tra('User can choose how many times the share page can be consulted'),
			'type' => 'flag',
			'dependencies' => array(
				'auth_token_access',
			),
			'default' => 'n',
		),
		'share_max_access_time' => array(
			'name' => tra('Maximum number of times accessed'),
			'description' => tra('Maximum number of times that the shared page can be consulted'),
			'type' => 'text',
			'default' => '-1',
		),
		
	);
}
