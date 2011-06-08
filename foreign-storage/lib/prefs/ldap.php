<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ldap_list() {
	return array(
		'ldap_create_user_tiki' => array(
			'name' => tra('If user does not exist in Tiki'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'y' => tra('Create the user'),
				'n' => tra('Deny access'),
			),
			'default' => 'y',
		),
		'ldap_create_user_ldap' => array(
			'name' => tra('Create user if not in LDAP'),
			'type' => 'flag',
			'default' => 'n',
		),
		'ldap_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
			'default' => 'y',
		),
	);	
}
