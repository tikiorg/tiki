<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_connect_list()
{
    return array (
		'connect_feature' => array(
			'name' => tra('Tiki Connect'),
            'description' => tra('Connect your Tiki with the community by sending anonymised statistical data to tiki.org'),
			'type' => 'flag',
			'default' => 'n',	// to be enabled by default when working for Tiki 8
			'tags' => array('experimental', 'basic'),
			'warning' => tra('Experimental. This feature is still under development.'),
			'admin' => 'connect',
			'help' => 'Connect',
		),
		'connect_send_info' => array(
			'name' => tra('Send Site Information'),
            'description' => tra('Additionally send keywords, location, etc. to tiki.org so you can connect with other Tiki sites near you.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'y',
			'tags' => array('basic'),
		),
		'connect_site_title' => array(
			'name' => tra('Site title'),
            'description' => tra('Name of site to be listed on Tiki Connect'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => array('basic'),
		),
		'connect_site_email' => array(
			'name' => tra('Email contact'),
            'description' => tra('Email to register'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => array('basic'),
		),
		'connect_site_url' => array(
			'name' => tra('URL'),
            'description' => tra('URL to register'),
			'type' => 'text',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => array('basic'),
		),
		'connect_site_keywords' => array(
			'name' => tra('Key words'),
            'description' => tra('Key words or tags describing your site'),
			'type' => 'textarea',
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => array('basic'),
		),
		'connect_site_location' => array(
			'name' => tra('Site location'),
            'description' => tra('Site location expressed as longitude, latitude, and zoom'),
			'type' => 'text',
			'size' => 60,
			'dependencies' => 'connect_send_info',
			'default' => '',
			'tags' => array('basic'),
		),
		'connect_send_anonymous_info' => array(
			'name' => tra('Send anonymous information'),
            'description' => tra('Send anonymous usage information.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'y',
		),
		'connect_frequency' => array(
			'name' => tra('Connection frequency'),
            'description' => tra('How often to send information (in hours)').' '.tra('Default:'). '168 ('.tra('weekly') . ')',
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'filter' => 'digits',
			'default' => '168',
			'warning' => tra('This feature is experimental and currently not in use. Stay tuned for updated information. Click "Send Info" to connect.'),
		),
		'connect_server' => array(
			'name' => tra('Tiki Connect server URL'),
            'description' => tra('Where to send the information.').' '.tra('Default:'). 'http://mother.tiki.org',
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'default' => 'https://mother.tiki.org',
			'filter' => 'url',
			'tags' => array('experimental'),
		),
		'connect_last_post' => array(
			'name' => tra('Last connection'),
            'description' => tra(''),
			'type' => 'text',
			'dependencies' => 'connect_feature',
			'filter' => 'digits',
			'default' => '',
			'tags' => array('experimental'),
		),
		'connect_server_mode' => array(
			'name' => tra('Connect server mode'),
            'description' => tra('For use by mother.tiki.org.'),
			'type' => 'flag',
			'dependencies' => 'connect_feature',
			'default' => 'n',
			'tags' => array('experimental'),
		),
		'connect_guid' => array(
			'name' => tra('Connect GUID'),
            'description' => tra('For use by mother.tiki.org. Do not modify'),
			'type' => 'text',
			'size' => 60,
			'dependencies' => 'connect_feature',
			'default' => '',
			'tags' => array('experimental', 'readonly'),	// TODO readonly tag?
		),
	);
}
