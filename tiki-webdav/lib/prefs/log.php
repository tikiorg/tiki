<?php

function prefs_log_list() {
	return array(
		'log_mail' => array(
			'name' => tra('Log mail in Tiki logs'),
			'description' => tra('Log mail in Tiki logs'),
			'type' => 'flag',
			'help' => 'System+Log',
		),
		'log_tpl' => array(
			'name' => tra('Smarty Template usage indicator'),
			'description' => tra('Add HTML comment at start and end of each Smarty template (TPL)'),
			'hint' => tra('Use only for developement, not in production because these warnings are added to emails as well, and visible to the users'),
			'type' => 'flag',
		),
		'log_sql' => array(
			'name' => tra('Log SQL'),
			'description' => tra('Log SQL queries'),
			'type' => 'flag',
		),
		'log_sql_perf_min' => array(
			'name' => tra('Log queries using more than (seconds)'),
			'description' => tra('This may impact performance'),
			'type' => 'text',
			'size' => 5,
			'dependencies' => array(
				'log_sql',
			),
		),
	);
}
