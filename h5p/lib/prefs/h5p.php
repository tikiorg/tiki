<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_h5p_list()
{
	return array(
		'h5p_enabled' => array(
			'name' => tra('H5P support'),
			'description' => tra('Handle H5P package files on upload.'),
			'dependencies' => array(
				'feature_file_galleries',
			),
			'type' => 'flag',
			'default' => 'n',
		),
		'h5p_whitelist' => array(
			'name' => tr('Whitelist'),
			'description' => tr('.'),
			'type' => 'text',
			'default' => H5PCore::$defaultContentWhitelist,
			'hint' => tr(''),
		),
	);
}

