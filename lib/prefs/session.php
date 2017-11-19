<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_session_list()
{
	return  [
		'session_storage' => [
			'name' => tra('Session storage location'),
			'description' => tra('Select where the session information should be stored. Memcache sessions require memcache to be configured.'),
			'type' => 'list',
			'perspective' => false,
			'options' => [
				'default' => tra('Default (from php.ini)'),
				'db' => tra('Database'),
				'memcache' => tra('Memcache'),
			],
			'default' => 'default',
		],
		'session_lifetime' => [
			'name' => tra('Session lifetime'),
			'description' => tra('Session lifetime'),
			'hint' => tra('Expressed as minutes. This is the minimum time logged in. To be the exact time, adjustments must be made in .htaccess session.gc_probability and session.gc_divisor to 1. Be careful - changing the probability may affect server performance.'),
			'type' => 'text',
			'filter' => 'digits',
			'units' => tra('minutes'),
			'perspective' => false,
			'size' => '4',
			'default' => 10080,
		],
		'session_silent' => [
			'name' => tra('Silent session'),
			'description' => tra('Do not automatically start sessions.'),
			'hint' => tra('Users will only have a session if they log in. Anonymous users will not have features like Switch Language or Switch Theme (which require a session)'),
			'warning' => tra('Can cause problems when combined with cookie consent and JavaScript-disabled browsers.'),
			'perspective' => false,
			'type' => 'flag',
			'default' => 'n',

			/* Tag experimental due to issues such as those documented above, and because PHP's session handling just doesn't allow reasonably supporting no sessions
			(writes to $_SESSION when no session is started don't even trigger a notice as of 5.6). The instance for which this preference was created no longer runs with session_silent.
			I/O performance improved a lot since this preference was introduced.
			Also just meant to hide by default, since this is is rarely justified (advanced). I am not sure any instance runs with this enabled. Chealer 2017-08-16 */
			'tags' => ['experimental']
		],
		'session_cookie_name' => [
			'name' => tra('Session cookie name'),
			'description' => tra('Session cookie name used instead of the PHP default configuration.'),
			'type' => 'text',
			'perspective' => false,
			'size' => 10,
			'default' => session_name(),
		],
		'session_protected' => [
			'name' => tra('Protect all sessions with HTTPS'),
			'description' => tra('Always redirect to HTTPS to prevent a session hijack through network sniffing.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			'warning' => tra('Warning: activate only if SSL is already configured; otherwise, all users including admin will be locked out of the site'),
			'tags' => ['advanced'],
		],
	];
}
