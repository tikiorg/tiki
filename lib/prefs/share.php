<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_share_list() {
	return array(
		'share_display_links' => array(
			'name' => tra('Display links in the share page'),
			'type' => 'flag'
		),
		'share_token_notification' => array(
			'name' => tra('Token notification'),
			'description' => tra('Users can subscribe to the notifications of consultation of the token'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'auth_token_access'
			),
		),
		'share_contact_add_non_existant_contact' => array(
			'name'	=> tra('Add contact'),
			'description' => tra('If not exist, recipients are add in the list of contacts'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'feature_contacts',
			),
		),
		'share_display_name_and_email' => array(
			'name' => tra('Display name and email'),
			'description' => tra('If user is connect, name and email display in the page'),
			'type' => 'flag',
		),
		'share_can_choose_how_much_time_access' => array(
			'name' => tra('How much time access'),
			'description' => tra('User can choose how much time the share page can be consult'),
			'type' => 'flag',
			'dependencies' => array(
				'auth_token_access',
			),
		),
		'share_max_access_time' => array(
			'name' => tra('Max how much time access'),
			'description' => tra('Maximum for select how much time the share page can be consult'),
			'type' => 'text',
		),
		
	);
}
