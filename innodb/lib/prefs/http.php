<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_http_list() {
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => '80',
		),
		'http_skip_frameset' => array(
			'name' => tra('HTTP Lookup Skip Framesets'),
			'description' => tra('When performing and HTTP request to an external source, verify if the result is a frameset and use heuristic to provide the real content.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);	
}
