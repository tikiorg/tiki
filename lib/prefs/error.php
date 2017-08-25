<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_error_list()
{
	return array(
		'error_reporting_adminonly' => array(
			'name' => tra('PHP errors visible to admin only'),
			'hint' => tra('During site development, it\'s better to display errors to all users. However, in production settings, errors should only be displayed to administrators.'),
			'type' => 'flag',
			'description' => tr('PHP Errors will be shown to only the Admin user.'),
			'default' => 'y',
		),
		'error_reporting_level' => array(
			'name' => tra('PHP Error reporting level'),
			'description' => tra('Level of errors to be reported. Errors can be seen in a collapsible box at the bottom of the page, if any exist.'),
			'type' => 'list',
			'options' => array(
				0 => tra('No error reporting'),
				2047 => tra('Report all PHP errors except strict'),
				-1 => tra('Report all PHP errors'),
				2039 => tra('Report all PHP errors except notices'),
				1 => tra('According to the PHP configuration')
			),
			'default' => 2039,	//	E_ALL & ~E_NOTICE
		),
	);
}
