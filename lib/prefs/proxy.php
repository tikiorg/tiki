<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_proxy_list()
{
	return  [
		'proxy_host' => [
			'name' => tra('Proxy host name'),
			'description' => tra('Proxy host - without http:// or similar, just the host name'),
			'type' => 'text',
			'size' => '20',
			'filter' => 'url',
			'dependencies' => [
				'use_proxy',
			],
			'default' => '',
		],
		'proxy_port' => [
			'name' => tra('Port'),
			'description' => tra('Proxy port'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '5',
			'dependencies' => [
				'use_proxy',
			],
			'default' => '',
		],
		'proxy_user' => [
			'name' => tra('Proxy username'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'none',
			'default' => '',
		],
		'proxy_pass' => [
			'name' => tra('Proxy password'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'none',
			'default' => '',
		],
	];
}
