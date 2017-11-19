<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_email_list()
{
	return [
		'email_due' => [
			'name' => tra('Re-validate user email after'),
			'description' => tra('The number of days after which an email will be sent to the user with a link to revalidate the account. The user will not be able to login (that is, the account will be invalid), until the user clicks the link. Use this feature to verify that a user’s email is still valid.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'units' => tra('days'),
			'hint' => tra('Use "-1" for never'),
			'default' => -1,
		],
		'email_footer' => [
			'name' => tra('Email footer'),
			'description' => tra('Text appended to outgoing emails.'),
			'type' => 'textarea',
			'size' => 5,
			'default' => '',
		],
	];
}
