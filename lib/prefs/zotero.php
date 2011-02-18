<?php

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
			'size' => 32,
		),
		'zotero_client_secret' => array(
			'name' => tra('Zotero Client Secret'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 32,
		),
		'zotero_group_id' => array(
			'name' => tra('Zotero Group'),
			'description' => tra('Numeric ID of the group, can be found in the URL.'),
			'type' => 'text',
			'filter' => 'digits',
		),
	);
}

