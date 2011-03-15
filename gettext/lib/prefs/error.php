<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_error_list() {
	return array(
		'error_reporting_adminonly' => array(
			'name' => tra('PHP errors visible to admin only'),
			'description' => tra('For development purposes, it\'s better to display errors for all users. However, in production settings, they should only be displayed to administrators.'),
			'type' => 'flag',
		),
		'error_reporting_level' => array(
			'name' => tra('PHP Error reporting level'),
			'description' => tra('Level from which errors should be reported.'),
			'type' => 'list',
			'options' => array(
				0 => tra('No error reporting'),
				2047 => tra('Report all PHP errors except strict'),
				-1 => tra('Report all PHP errors'),
				2039 => tra('Report all PHP errors except notices'),
				1 => tra('According to PHP configuration')
			),
		),
	);
}
