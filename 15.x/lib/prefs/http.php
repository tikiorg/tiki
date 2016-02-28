<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_http_list()
{
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
            'description' => tra('The port used to access this server; if not specified, port 80 will be used'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => '',
			'shorthint' => tra('If not specified, port 80 will be used'),
		),
		'http_skip_frameset' => array(
			'name' => tra('HTTP lookup: skip framesets'),
			'description' => tra('When performing an HTTP request to an external source, verify if the result is a frameset and use heuristic to provide the real content.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'http_referer_registration_check' => array(
			'name' => tra('Registration referrer check'),
			'description' => tra('Use the HTTP referrer to check registration POST is sent from same host. (May not work on some setups.)'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
