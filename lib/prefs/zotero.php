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
			'name' => tra('Zotero bibliography'),
			'help' => 'Zotero',
			'description' => tra('Connect Tiki to the <a href="https://www.zotero.org">Zotero</a> online bibliography management system.'),
			'type' => 'flag',
			'hint' => tr('You must supply the following items: Zotero Client Key, Zotero Client Secret, Zotero Group, and Zotero Reference Style.'),
			'default' => 'n',
		),
		'zotero_client_key' => array(
			'name' => tra('Zotero client key'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'zotero_client_secret' => array(
			'name' => tra('Zotero client secret'),
			'description' => tra('Required identification key. Registration required.'),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'zotero_group_id' => array(
			'name' => tra('Zotero group ID'),
			'description' => tra('Numeric ID of the group, can be found in the URL.'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 7,
			'default' => '',
		),
		'zotero_style' => array(
			'name' => tra('Zotero reference style'),
			'description' => tra('Use an alternate Zotero reference style when formatting the references. The reference formats must be installed on the Zotero server.'),
			'type' => 'text',
			'filter' => 'text',
			'size' => 20,
			'default' => '',
		),
	);
}

