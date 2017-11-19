<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_connect_list()
{
	return  [
		'connect_feature' => [
			'name' => tra('Tiki connect'),
			'description' => tra('Connect your Tiki with the community by sending anonymised statistical data to tiki.org'),
			'type' => 'flag',
			'default' => 'n',	// to be enabled by default when working for Tiki 8
			'tags' => ['experimental', 'basic'],
			'warning' => tra('This feature is still under development.'),
			'admin' => 'connect',
			'help' => 'Connect',
		],
		'connect_send_info' => [
			'name' => tra('Send site information'),
			'description' => tra('Additionally send keywords, location, etc. to tiki.org so you can connect with other Tiki sites near you.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'y',
			'tags' => ['basic'],
		],
		'connect_site_title' => [
			'name' => tra('Site title'),
			'description' => tra('Name of site to be listed on Tiki Connect'),
			'warning' => tra('Site title is required to send site information.'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => ['basic'],
		],
		'connect_site_email' => [
			'name' => tra('Email contact'),
			'description' => tra('Email to register'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => ['basic'],
		],
		'connect_site_url' => [
			'name' => tra('URL'),
			'description' => tra('URL to register'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => ['basic'],
		],
		'connect_site_keywords' => [
			'name' => tra('Key words'),
			'description' => tra('Key words or tags describing your site'),
			'type' => 'textarea',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => ['basic'],
		],
		'connect_site_location' => [
			'name' => tra('Site location'),
			'description' => tra('Site location expressed as longitude, latitude, and zoom'),
			'type' => 'text',
			'size' => 60,
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => ['basic'],
		],
		'connect_send_anonymous_info' => [
			'name' => tra('Send anonymous information'),
			'description' => tra('Send anonymous usage information.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'y',
		],
		'connect_frequency' => [
			'name' => tra('Connection frequency'),
			'description' => tra('How often to send information'),
			'units' => tra('hours'),
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'filter' => 'digits',
			'hint' => tr('Click "Send Info" to connect.'),
			'default' => '168',
			'warning' => tra('Currently not in use.'),
			'tags' => ['experimental'],
		],
		'connect_server' => [
			'name' => tra('Tiki connect server URL'),
			'description' => tra('Where to send the information.'),
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'default' => 'https://mother.tiki.org',
			'filter' => 'url',
			'tags' => ['experimental'],
		],
		'connect_last_post' => [
			'name' => tra('Last connection'),
			'description' => tra(''),
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'filter' => 'digits',
			'default' => '',
			'tags' => ['experimental'],
		],
		'connect_server_mode' => [
			'name' => tra('Connect server mode'),
			'description' => tra('For use by mother.tiki.org.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'n',
			'tags' => ['experimental'],
		],
		'connect_guid' => [
			'name' => tra('Connect GUID'),
			'description' => tra('For use by mother.tiki.org. Do not modify'),
			'type' => 'text',
			'size' => 60,
			'dependencies' => 'connect_feature',
			'default' => '',
			'tags' => ['experimental', 'readonly'],	// TODO readonly tag?
		],
	];
}
