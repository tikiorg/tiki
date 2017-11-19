<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_xmpp_list()
{
	return [
		'xmpp_feature' => [
			'name' => tra('XMPP client (ConverseJS)'),
			'description' => tra('Integration with Converse.js XMPP client.'),
			'type' => 'flag',
			'keywords' => 'xmpp converse conversejs chat',
			'help' => 'XMPP',
			'tags' => ['basic'],
			'default' => 'n',
			'extensions' => [
			],
		],
		'xmpp_server_host' => [
			'name' => tra('XMPP server host'),
			'description' => tra('XMPP server host'),
			'type' => 'text',
			'filter' => 'text',
			'hint' => tra('xmpp.example.com'),
			'keywords' => 'xmpp converse conversejs chat',
			'size' => 40,
			'tags' => ['basic'],
			'default' => '',
		],
		'xmpp_server_http_bind' => [
			'name' => tra('XMPP http-bind URL'),
			'description' => tra('Full URL to the http-bind.'),
			'keywords' => 'xmpp converse conversejs chat',
			'type' => 'text',
			'size' => 40,
			'filter' => 'url',
			'hint' => tra('http://xmpp.example.com/http-bind/'),
			'tags' => ['basic'],
			'default' => '',
		],
		'xmpp_openfire_use_token' => [
			'name' => tra('XMPP Openfire Token'),
			'default' => 'n',
			'description' => tra('Handle user authentication using tokens'),
			'keywords' => 'xmpp openfire token',
			'type' => 'flag',
			'tags' => ['basic'],
			'default' => '',
		]
	];
}
