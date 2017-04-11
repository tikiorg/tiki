<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_webcron_list()
{
	return array(
		'webcron_enabled' => array(
			'name' => tra('Enabled'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'webcron_type' => array(
			'name' => tra('How to trigger Web Cron'),
			'description' => tra(''),
			'type' => 'list',
			'options' => array(
				'url' => tra('Calling the Web Cron URL'),
				'js' => tra('Adding JavaScript that calls Web Cron'),
				'both' => tra('URL and JavaScript'),
			),
			'default' => 'both',
		),
		'webcron_run_interval' => array(
			'name' => tra('Run interval (seconds)'),
			'description' => tra('The amount of time of each run'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'units' => tra('seconds'),
			'default' => 60,
		),
		'webcron_token' => array(
			'name' => tra('Token'),
			'description' => tra('The token to use when running the cron manually'),
			'type' => 'text',
			'default' => md5(phpseclib\Crypt\Random::string(100)),
		),
	);
}
