<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ids_list()
{
	return array(
		'ids_enabled' => array(
			'name' => tra('Enable intrusion detection system'),
			'description' => tra('An intrusion detection system (IDS) is a device or software application that monitors a network or systems for malicious activity or policy violations.'),
			'type' => 'flag',
			'default' => 'n',
			'packages_required' => array('enygma/expose'=>'Expose\Manager'),
		),
		'ids_log_to_file' => array(
			'name' => tra('Log to file'),
			'type' => 'text',
			'default' => 'ids.log',
			'dependencies' => array(
				'ids_enabled',
			),
		),
		'ids_log_to_database' => array(
			'name' => tra('Log to database'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'ids_enabled',
			),
		),
	);
}

