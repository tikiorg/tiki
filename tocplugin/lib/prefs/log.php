<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_log_list()
{
	return array(
		'log_mail' => array(
			'name' => tra('Log mail in Tiki logs'),
			'description' => tra('Log mail in Tiki logs'),
			'type' => 'flag',
			'help' => 'System+Log',
			'default' => 'n',
		),
		'log_tpl' => array(
			'name' => tra('Smarty template usage indicator'),
			'description' => tra('Add HTML comment at start and end of each Smarty template (.tpl file)'),
			'hint' => tra('Use only for development, not in production because these warnings are added to emails as well, and visible to the users'),
			'warning' => tra('You need to clear your Tiki template cache for this change to take effect'),
			'type' => 'flag',
			'default' => 'n',
		),
		'log_sql' => array(
			'name' => tra('Log SQL'),
			'description' => tra('Log SQL queries'),
			'type' => 'flag',
			'default' => 'n',
		),
		'log_sql_perf_min' => array(
			'name' => tra('Log queries using more than (seconds)'),
			'description' => tra('This may impact performance'),
			'type' => 'text',
			'size' => 5,
			'dependencies' => array(
				'log_sql',
			),
			'default' => '0.05',
		),
	);
}
