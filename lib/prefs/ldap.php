<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ldap_list()
{
	return array(
		'ldap_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'description' => tra('If a user was authenticated via LDAP, but not found in the Tiki user database, Tiki will create an entry in its user database if this option is checked.'),
			'type' => 'list',
			'warning' => tra('If this option is disabled, this user wouldn’t be able to log in.'),
			'perspective' => false,
			'options' => array(
				'y' => tra('Create the user'),
				'n' => tra('Deny access'),
			),
			'default' => 'y',
		),
		'ldap_create_user_ldap' => array(
			'name' => tra('Create user if not in LDAP'),
			'description' => tra('If a user was authenticated by Tiki’s user database, but not found on the LDAP server, Tiki will create an LDAP entry for this user.'),
			'type' => 'flag',
			'default' => 'n',
			'warning' => 'As of this writing, this is not yet implemented, and this option will probably not be offered in future.',
			'tags' => array('experimental'),
		),
		'ldap_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'description' => tra('If this option is set, the user “admin” will be authenticated by only using Tiki’s user database and not via LDAP. This option has no effect on users other than “admin”.'),
			'type' => 'flag',
			'default' => 'y',
		),
	);	
}
