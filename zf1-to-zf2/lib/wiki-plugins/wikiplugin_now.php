<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_now_info() 
{
	return array(
		'name' => tra('Now'),
		'documentation' => 'PluginNow',
		'description' => tra('Show current date and time'),
		'prefs' => array('wikiplugin_now'),
		'body' => tra('text'),
		'iconname' => 'history',
		'introduced' => 9,
		'tags' => array( 'basic' ),
		'params' => array(
			'format' => array(
				'required' => false,
				'name' => tra('Format'),
				'description' => tra('Time format'),
				'since' => '9.0',
				'default' => '%A %e %B %Y %H:%M',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_now($data, $params) 
{
	extract($params, EXTR_SKIP);
	return TikiLib::date_format(tra($format));
}
