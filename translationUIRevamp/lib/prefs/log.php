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
			'name' => tra('Add HTML comment at start and end of each Smarty template (TPL)'),
			'description' => tra('Add HTML comment at start and end of each Smarty template (TPL)'),
			'type' => 'flag',
		),
		'log_sql' => array(
			'name' => tra('Log SQL'),
			'description' => tra('Log SQL'),
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
