<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ids_list()
{
	return array(
		'ids_enabled' => array(
			'name' => tra('Intrusion Detection System'),
			'description' => tra('Use PHPIDS to check the input to pages to discover malicious requests.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'ids_single_threshold' => array(
			'name' => tra('Per-request maximum impact'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 25,
		),
		'ids_session_threshold' => array(
			'name' => tra('Per-session impact'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 150,
		),
	);
}

