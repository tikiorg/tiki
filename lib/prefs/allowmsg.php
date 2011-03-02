<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_allowmsg_list() {
	return array(
		'allowmsg_by_default' => array(
			'name' => tra('Users accept internal messages by default'),
			'description' => tra('Users accept internal messages by default'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
		),
		'allowmsg_is_optional' => array(
			'name' => tra('Users can opt-out of internal messages'),
			'description' => tra('Users can opt-out of internal messages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
		),
	);
}
