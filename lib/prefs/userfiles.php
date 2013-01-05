<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_userfiles_list()
{
	return array(
		'userfiles_quota' => array(
			'name' => tra('Quota (MB)'),
			'type' => 'text',
			'size' => 5,
			'default' => 30,
		),
		'userfiles_private' => array(
			'name' => tra('Private'),
			'description' => tra("Users can see each other's files"),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
