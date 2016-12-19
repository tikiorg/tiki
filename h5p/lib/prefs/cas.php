<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_cas_list()
{
	return array(
		'cas_create_user_tiki' => array(
			'name' => tra('Create user if not already a registered user'),
            'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			),
		'cas_autologin' => array(
			'name' => tra('Try automatically to connect SSO'),
            'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			),
		'cas_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
            'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			),
		'cas_force_logout' => array(
			'name' => tra('Force CAS log-out when the user logs out from Tiki.'),
            'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			),
		'cas_show_alternate_login' => array(
			'name' => tra('Show alternate log-in method in header'),
            'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'y',
			),
		'cas_version' => array(
			'name' => tra('CAS server version'),
            'description' => tra(''),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'none' => tra('none'),
				'1.0'  => tra('Version 1.0'),
				'2.0'  => tra('Version 2.0'),
				),
			'default' => '1.0',
			),
		'cas_hostname' => array(
			'name' => tra('Hostname'),
			'description' => tra('Hostname of the CAS server.'),
			'type' => 'text',
			'size' => 50,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			),
		'cas_port' => array(
			'name' => tra('Port'),
			'description' => tra('Port of the CAS server.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'perspective' => false,
			'default' => '443',
			),
		'cas_path' => array(
			'name' => tra('Path'),
			'description' => tra('Path for the CAS server.'),
			'type' => 'text',
			'size' => 50,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			),
		'cas_extra_param' => array(
			'name' => tra('CAS Extra Parameter'),
			'description' => tra('Extra Parameter to pass to the CAS Server.'),
			'type' => 'text',
			'size' => 100,
			'filter' => 'striptags',
			'perspective' => false,
			'default' => '',
			),
		'cas_authentication_timeout' => array(
			'name' => tra('CAS Authentication Verification Timeout'),
			'description' => tra('Verify authentication with the CAS server every N seconds. Null value means never reverify.'),
			'type' => 'list',
			'filter' => 'digits',
			'perspective' => false,
			'options' => array (
				'0' => tra('Never'),
				'60' => tra('1 minute'),
				'120' => tra('2 minutes'),
				'300' => tra('5 minutes'),
				'600' => tra('10 minutes'),
				'900' => tra('15 minutes'),
				'1800' => tra('30 minutes'),
				'3600' => tra('1 hour'),
				),
			'default' => '0',
			),
		);
}
