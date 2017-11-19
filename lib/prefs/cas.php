<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_cas_list()
{
	return [
		'cas_create_user_tiki' => [
			'name' => tra('Create user if not registered in Tiki'),
			'type' => 'flag',
			'description' => tr('If a user was externally authenticated, but not found in the Tiki user database, Tiki will create an entry in its user database.'),
			'perspective' => false,
			'default' => 'n',
			],
		'cas_autologin' => [
			'name' => tra('Try automatically to connect SSO'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			],
		'cas_skip_admin' => [
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
			'description' => tra('The user “admin” will be authenticated by <b>only</b> using Tiki’s user database. This option has no effect on users other than “admin”.'),
			'perspective' => false,
			'default' => 'n',
			],
		'cas_force_logout' => [
			'name' => tra('Force CAS log-out when the user logs out from Tiki.'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			],
		'cas_show_alternate_login' => [
			'name' => tra('Show alternate log-in method in header'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'y',
			],
		'cas_version' => [
			'name' => tra('CAS server version'),
			'description' => tra(''),
			'type' => 'list',
			'perspective' => false,
			'options' => [
				'none' => tra('none'),
				'1.0'  => tra('Version 1.0'),
				'2.0'  => tra('Version 2.0'),
				],
			'default' => '1.0',
			],
		'cas_hostname' => [
			'name' => tra('Hostname'),
			'description' => tra('Hostname of the CAS server.'),
			'type' => 'text',
			'size' => 50,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			],
		'cas_port' => [
			'name' => tra('Port'),
			'description' => tra('Port of the CAS server.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'perspective' => false,
			'default' => '443',
			],
		'cas_path' => [
			'name' => tra('Path'),
			'description' => tra('Path for the CAS server.'),
			'type' => 'text',
			'size' => 50,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			],
		'cas_extra_param' => [
			'name' => tra('CAS Extra Parameter'),
			'description' => tra('Extra Parameter to pass to the CAS Server.'),
			'type' => 'text',
			'size' => 100,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			],
		'cas_authentication_timeout' => [
			'name' => tra('CAS Authentication Verification Timeout'),
			'description' => tra('Verify authentication with the CAS server every N seconds. Null value means never reverify.'),
			'type' => 'list',
			'filter' => 'digits',
			'perspective' => false,
			'options' => [
				'0' => tra('Never'),
				'60' => '1 ' . tra('minute'),
				'120' => '2 ' . tra('minutes'),
				'300' => '5 ' . tra('minutes'),
				'600' => '10 ' . tra('minutes'),
				'900' => '15 ' . tra('minutes'),
				'1800' => '30 ' . tra('minutes'),
				'3600' => '1 ' . tra('hour'),
				],
			'default' => '0',
			],
		];
}
