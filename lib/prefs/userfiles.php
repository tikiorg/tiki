<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_userfiles_list()
{
	return [
		'userfiles_quota' => [
			'name' => tra('Quota'),
			'type' => 'text',
			'size' => 5,
			'units' => tra('megabytes'),
			'default' => 30,
			'dependencies' => [
				'feature_userfiles',
			],
		],
		'userfiles_private' => [
			'name' => tra('Private'),
			'description' => tra("Users cannot see each other's files or galleries"),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => [
				'feature_userfiles',
			],
		],
		'userfiles_hidden' => [
			'name' => tra('Hidden'),
			'description' => tra("Users can see each other's files, but don't see the galleries in listings"),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => [
				'feature_userfiles',
			],
		],
	];
}
