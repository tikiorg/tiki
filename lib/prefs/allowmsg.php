<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_allowmsg_list()
{
	return [
		'allowmsg_by_default' => [
			'name' => tra('Users accept internal messages by default'),
			'type' => 'flag',
			'dependencies' => [
				'feature_messages',
			],
			'default' => 'y',
		],
		'allowmsg_is_optional' => [
			'name' => tra('Users can opt out of internal messages'),
			'type' => 'flag',
			'dependencies' => [
				'feature_messages',
			],
			'default' => 'y',
		],
	];
}
