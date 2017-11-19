<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_memcache_list()
{
	return [
		'memcache_enabled' => [
			'name' => tra('Memcache'),
			'description' => tra('Enable connection to memcached servers to store temporary information.'),
			'type' => 'flag',
			'hint' => tra('Requires the PHP memcache extension.'),
			'extensions' => [ 'memcache' ],
			'default' => 'n',
		],
		'memcache_compress' => [
			'name' => tra('Memcache compression'),
			'description' => tra('Enable compression for memcache storage.'),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => [ 'memcache' ],
		],
		'memcache_servers' => [
			'name' => tra('Memcache servers'),
			'description' => tra('Server list which may be accessed. For each server, the address, port and weight must be specified.'),
			'type' => 'textarea',
			'filter' => 'striptags',
			'hint' => tra('One per line, with format of __address:port (weight)__; for example, "localhost:11211 (1)"'),
			'serialize' => 'prefs_memcache_serialize_servers',
			'unserialize' => 'prefs_memcache_unserialize_servers',
			'size' => 10,
			'default' => false,
			'extensions' => [ 'memcache' ],
		],
		'memcache_prefix' => [
			'name' => tra('Memcache key prefix'),
			'description' => tra('When the memcache cluster is used by multiple applications, using unique prefixes for each of them helps avoid conflicts.'),
			'filter' => 'word',
			'size' => 10,
			'type' => 'text',
			'default' => 'tiki_',
			'extensions' => [ 'memcache' ],
		],
		'memcache_expiration' => [
			'name' => tra('Memcache expiration'),
			'description' => tra('Duration for which the data will be kept.'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
			'units' => tra('seconds'),
			'default' => 3600,
			'extensions' => [ 'memcache' ],
		],
		'memcache_wiki_data' => [
			'name' => tra('Cache wiki data in memcache'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => [ 'memcache' ],
		],
		'memcache_wiki_output' => [
			'name' => tra('Cache wiki output in memcache'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => [ 'memcache' ],
		],
		'memcache_forum_output' => [
			'name' => tra('Cache forum output in memcache'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => [ 'memcache' ],
		],
	];
}

function prefs_memcache_serialize_servers($data)
{
	if (! is_array($data)) {
		$data = unserialize($data);
	}
	$out = '';
	if (is_array($data)) {
		foreach ($data as $row) {
			$out .= "{$row['host']}:{$row['port']} ({$row['weight']})\n";
		}
	}

	return trim($out);
}

function prefs_memcache_unserialize_servers($string)
{
	$data = [];

	foreach (explode("\n", $string) as $row) {
		if (preg_match("/^\s*([^:]+):(\d+)\s*(\((\d+)\))?\s*$/", $row, $parts)) {
			$data[] = [
				'host' => $parts[1],
				'port' => $parts[2],
				'weight' => isset($parts[4]) ? $parts[4] : 1,
			];
		}
	}

	if (count($data)) {
		return $data;
	} else {
		return false;
	}
}
