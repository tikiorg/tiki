<?php

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
