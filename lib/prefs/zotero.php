<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_zotero_list()
{
	return array(
		'zotero_enabled' => array(
			'name' => tra('Zotero Bibliography'),
			'help' => 'Zotero',
			'description' => tra('Connect Tiki to the Zotero online bibliography management system.'),
			'type' => 'flag',
		),
		'zotero_client_key' => array(
			'name' => tra('Zotero Client Key'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
		),
		'zotero_client_secret' => array(
			'name' => tra('Zotero Client Secret'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
		),
		'zotero_group_id' => array(
			'name' => tra('Zotero Group'),
			'description' => tra('Numeric ID of the group, can be found in the URL.'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 7,
		),
	);
}

