<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_memcache_list()
{
	return array(
		'memcache_enabled' => array(
			'name' => tra('Memcache'),
			'description' => tra('Enable connection to memcached servers to store temporary information.'),
			'type' => 'flag',
			'hint' => tra('Requires the PHP memcache extension.'),
			'extensions' => array( 'memcache' ),
			'default' => 'n',
		),
		'memcache_compress' => array(
			'name' => tra('Memcache compression'),
			'description' => tra('Enable compression for memcache storage.'),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => array( 'memcache' ),
		),
		'memcache_servers' => array(
			'name' => tra('Memcache servers'),
			'description' => tra('Server list which may be accessed. For each server, the address, port and weight must be specified.'),
			'type' => 'textarea',
			'filter' => 'striptags',
			'hint' => tra('One per line, with format of address:port (weight); for example, "localhost:11211 (1)"'),
			'serialize' => 'prefs_memcache_serialize_servers',
			'unserialize' => 'prefs_memcache_unserialize_servers',
			'size' => 10,
			'default' => false,
			'extensions' => array( 'memcache' ),
		),
		'memcache_prefix' => array(
			'name' => tra('Memcache key prefix'),
			'description' => tra('When the memcache cluster is used by multiple applications, using unique prefixes for each of them helps avoid conflicts.'),
			'filter' => 'word',
			'size' => 10,
			'type' => 'text',
			'default' => 'tiki_',
			'extensions' => array( 'memcache' ),
		),
		'memcache_expiration' => array(
			'name' => tra('Memcache expiration'),
			'description' => tra('Duration for which the data will be kept.'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
			'default' => 3600,
			'extensions' => array( 'memcache' ),
		),
		'memcache_wiki_data' => array(
			'name' => tra('Cache wiki data in memcache'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => array( 'memcache' ),
		),
		'memcache_wiki_output' => array(
			'name' => tra('Cache wiki output in memcache'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => array( 'memcache' ),
		),
		'memcache_forum_output' => array(
			'name' => tra('Cache forum output in memcache'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
			'extensions' => array( 'memcache' ),
		),
	);
}

function prefs_memcache_serialize_servers( $data )
{
	if ( ! is_array($data) ) {
		$data = unserialize($data);
	}
	$out = '';
	if (is_array($data)) {
		foreach ( $data as $row ) {
			$out .= "{$row['host']}:{$row['port']} ({$row['weight']})\n";
		}
	}

	return trim($out);
}

function prefs_memcache_unserialize_servers( $string )
{
	$data = array();

	foreach ( explode("\n", $string) as $row) {
		if ( preg_match("/^\s*([^:]+):(\d+)\s*(\((\d+)\))?\s*$/", $row, $parts) ) {
			$data[] = array(
				'host' => $parts[1],
				'port' => $parts[2],
				'weight' => isset( $parts[4] ) ? $parts[4] : 1,
			);
		}
	}

	if ( count($data) ) {
		return $data;
	} else {
		return false;
	}
}
