<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_xmpp_list()
{
	return array(
		'xmpp_feature' => array(
			'name' => tra('XMPP client (ConverseJS)'),
			'description' => tra('Integration with Converse.js XMPP client.'),
			'type' => 'flag',
			'keywords' => 'xmpp converse conversejs chat',
			'help' => 'XMPP',
			'tags' => array('basic'),
			'default' => 'n',
			'extensions' => array(
			),			
		),
		'xmpp_server_host' => array(
			'name' => tra('XMPP server host'),
			'description' => tra('XMPP server host.'),
			'type' => 'text',
			'filter' => 'text',
			'hint' => tra('xmpp.example.com'),
			'keywords' => 'xmpp converse conversejs chat',
			'size' => 40,
			'tags' => array('basic'),
			'default' => '',
		),
		'xmpp_server_http_bind' => array(
			'name' => tra('XMPP http-bind url'),
			'description' => tra('Full URL to the http-bind.'),
			'keywords' => 'xmpp converse conversejs chat',
			'type' => 'text',
			'size' => 40,
			'filter' => 'url',
			'hint' => tra('http://xmpp.example.com/http-bind/'),
			'tags' => array('basic'),
			'default' => '',
		),
		'xmpp_openfire_use_token' => array(
			'name' => tra('XMPP Openfire Token'),
			'default' => 'n',
			'description' => tra('Handle user authentication using tokens'),
			'keywords' => 'xmpp openfire token',
			'type' => 'flag',
			'tags' => array('basic'),
			'default' => '',
		)
	);
}


