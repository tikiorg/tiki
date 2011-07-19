<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_session_list() {
	return array (
		'session_storage' => array(
			'name' => tra('Session storage location'),
			'description' => tra('Select where the session information should be stored. Memcache sessions require memcache to be configured.'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'default' => tra('Default (from php.ini)'),
				'db' => tra('Database'),
				'memcache' => tra('Memcache'),
			),
			'default' => 'default',
		),
		'session_lifetime' => array(
			'name' => tra('Session lifetime'),
			'description' => tra('Session lifetime'),
			'hint' => tra('Value provided in minutes. It will be the minimum time you are logged in. If you want it to be the exact time, you need to adjust in .htaccess session.gc_probability and session.gc_divisor to 1. Be careful that changing the probability will affect the server performance.'),
			'type' => 'text',
			'filter' => 'digits',
			'perspective' => false,
			'size' => '4',
			'default' => 0,
		),
		'session_silent' => array(
			'name' => tra('Silent session'),
			'description' => tra('Do not automatically start sessions.'),
			'perspective' => false,
			'type' => 'flag',
			'default' => 'n',
		),
		'session_cookie_name' => array(
			'name' => tra('Session cookie name'),
			'description' => tra('Session cookie name used instead of the PHP default configuration.'),
			'type' => 'text',
			'perspective' => false,
			'size' => 10,
			'default' => session_name(),
		),
		'session_protected' => array(
			'name' => tra('Protect all sessions'),
			'description' => tra('Always redirect to HTTPS to prevent session hijack through network sniffing.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
	);
}
