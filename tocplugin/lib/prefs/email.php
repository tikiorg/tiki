<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_email_list()
{
	return array(
		'email_due' => array(
			'name' => tra('Re-validate user by email after'),
            'description' => tra('Number of days to wait before re-validating the user\'s email address'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'shorthint' => tra('days'),
			'hint' => tra('Use "-1" for never'),
			'default' => -1,
		),
		'email_footer' => array(
			'name' => tra('Email footer'),
			'description' => tra('Text appended to outgoing emails.'),
			'type' => 'textarea',
			'size' => 5,
			'default' => '',
		),
	);
}
