<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_desactive_list()
{
	return [
		'desactive_login_autocomplete' => [
			'name' => tra("Disable browser's autocomplete feature for username and password fields"),
			'description' => tra('Use to deactivate the autocomplete in the login box. The autocomplete features can be optionally set in the user’s browser to remember the form input and proposes the remember the password. If enabled, the user login and password can not be remembered. You should enable this feature for highly secure sites.'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
