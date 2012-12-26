<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ids_list()
{
	return array(
		'ids_enabled' => array(
			'name' => tra('Intrusion Detection System'),
			'description' => tra('Use PHPIDS to check the input to pages to discover malicious requests.'),
			'type' => 'flag',
			'default' => 'n',
			'help' => 'PHPIDS',
			'tags' => array('experimental'),
			'keywords' => 'attack security rce csrf lfi exploit sqli phpids header filter security injection xss sql hacking ids directory traversal attacks injections detection intrusion ldap dt',
			'warning' => tra("This feature is not ready yet. Don't use yet unless you are a developer and are ready to help make it work. You will likely just lock yourself out of your site."),
		),
		'ids_single_threshold' => array(
			'name' => tra('Per-request maximum impact'),
            'description' => tra(''),
			'type' => 'text',
			'filter' => 'int',
			'default' => 25,
			'tags' => array('experimental'),
			'warning' => tra("This feature is not ready yet. Don't use yet unless you are a developer and are ready to help make it work. You will likely just lock yourself out of your site."),
		),
		'ids_session_threshold' => array(
			'name' => tra('Per-session impact'),
            'description' => tra(''),
			'type' => 'text',
			'filter' => 'int',
			'default' => 150,
			'tags' => array('experimental'),
			'warning' => tra("This feature is not ready yet. Don't use yet unless you are a developer and are ready to help make it work. You will likely just lock yourself out of your site."),
		),
	);
}

