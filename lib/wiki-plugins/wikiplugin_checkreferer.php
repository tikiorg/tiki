<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_checkreferer_info()
{
	return array(
		'name' => tra('Check Referer'),
		'documentation' => 'PluginCheckReferer',
		'description' => tra('Checks if the server HTTP_REFERER matches one of the referers specified in the parameter and displays content accordingly.'),
		'prefs' => array('wikiplugin_checkreferer'),
		'icon' => 'img/icons/database_go.png',
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed if conditions are not met.'),
		'params' => array(
			'referer_list' => array(
				'required' => true,
				'name' => tra('Referer List'),
				'description' => tra('Comma separated list of domains to check.'),
				'separator' => ',',
				'default' => '',
			),
		),
	);
}

function wikiplugin_checkreferer( $data, $params )
{
	$referer = parse_url($_SERVER['HTTP_REFERER']);
	$parts = explode('{ELSE}', $data);

	foreach ($params['referer_list'] as $allowed) {
		if ($referer['host'] === $allowed) {
			return $parts[0];
		}
	}
	if (count($parts) > 1) {
		return $parts[1];
	} else {
		return '';
	}
}
