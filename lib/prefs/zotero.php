<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
			'default' => 'n',
		),
		'zotero_client_key' => array(
			'name' => tra('Zotero Client Key'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'zotero_client_secret' => array(
			'name' => tra('Zotero Client Secret'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'zotero_group_id' => array(
			'name' => tra('Zotero Group ID'),
			'description' => tra('Numeric ID of the group, can be found in the URL.'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 7,
			'default' => '',
		),
		'zotero_style' => array(
			'name' => tra('Zotero Reference Style'),
			'description' => tra('Use an alternate Zotero reference style when formatting the references. The reference formats must be installed on the Zotero server.'),
			'type' => 'text',
			'filter' => 'text',
			'size' => 20,
			'default' => '',
		),
	);
}

