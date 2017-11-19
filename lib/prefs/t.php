<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_t_list()
{
	return [
		't_use_db' => [
			'name' => tra('Storage'),
			'description' => tra('Location where tracker attachment files are stored'),
			'type' => 'list',
			'options' => [
				'y' => tra('Database'),
				'n' => tra('Filesystem'),
			],
			'default' => 'y',
			'tags' => ['basic'],
		],
		't_use_dir' => [
			'name' => tra('Directory path'),
			'description' => tra("Path of a directory on Tiki's host, such as /var/www/. For confidentiality, this directory should not be web accessible. PHP must be able to read/write to the directory."),
			'type' => 'text',
			'size' => 50,
			'default' => '',
			'tags' => ['basic'],
		],
	];
}
