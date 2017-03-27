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
			'description' => tra('Storage settings for tacker attachment files'),
			'type' => 'list',
			'options' => [
				'y' => tra('Store in database'),
				'n' => tra('Store in directory'),
			],
			'default' => 'y',
			'tags' => ['basic'],
		],
		't_use_dir' => [
			'name' => tra('Directory path'),
			'description' => tra("Specify a directory on your server, for example: /var/www/  It's recommended that this directory not be web accessible. PHP must be able to read/write to the directory."),
			'type' => 'text',
			'size' => 50,
			'default' => '',
			'tags' => ['basic'],
		],
	];
}
