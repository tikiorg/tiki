<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_login_list() {
	return array(
		'login_is_email' => array(
			'name' => tra('Use email as username'),
			'description' => tra('Instead of creating new usernames, use the user\'s email address for authentication.'),
			'type' => 'flag',
		),
		'login_is_email_obscure' => array(
			'name' => tra('Obscure email when using email as username if possible (coverage will not be complete)'),
			'description' => tra('This will attempt as much as possible to hide the email, showing the realname or the truncated email instead.'),
			'type' => 'flag',
			'dependencies' => array(
				'login_is_email',
			),
		),
	);
}

