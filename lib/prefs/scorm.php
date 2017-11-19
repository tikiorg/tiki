<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_scorm_list()
{
	return [
		'scorm_enabled' => [
			'name' => tra('SCORM support'),
			'description' => tra('Handle SCORM package files on upload. SCORM is a standard used in learning management systems.'),
			'dependencies' => [
				'feature_file_galleries',
				'feature_trackers',
			],
			'type' => 'flag',
			'default' => 'n',
		],
		'scorm_tracker' => [
			'name' => tra('SCORM Tracker'),
			'description' => tra('The ID number of the Tracker used to store SCORM metadata'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 0,
			'size' => 5,
			'profile_reference' => 'tracker',
		],
	];
}
