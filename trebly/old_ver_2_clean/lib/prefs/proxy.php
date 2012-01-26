<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_proxy_list() {
	return array (
		'proxy_host' => array(
			'name' => tra('Host'),
			'description' => tra('Proxy host'),
			'type' => 'text',
			'size' => '20',
			'dependencies' => array(
				'use_proxy',
			),
		),
		'proxy_port' => array(
			'name' => tra('Port'),
			'description' => tra('Proxy port'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '5',
			'dependencies' => array(
				'use_proxy',
			),
		),
	);
}
