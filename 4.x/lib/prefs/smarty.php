<?php

function prefs_smarty_list() {
	return array(
		'smarty_notice_reporting' => array(
			'name' => tra('Include smarty notices in PHP error report'),
			'description' => tra('In most cases, smarty notices can be safely ignored. However, they may be useful in the development process when strange issues occur.'),
			'type' => 'flag',
		),
		'smarty_security' => array(
			'name' => tra('Smarty Security'),
			'description' => tra('Smarty Security'),
			'warning' => tra('Do not allow php code in smarty templates.'),
			'type' => 'flag',
			'help' => tra(''),
		),
	);
}
