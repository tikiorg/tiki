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
				'description' => tr('Time format using the PHP format described here: %0',
					'http://www.php.net/manual/en/function.strftime.php'),
				'since' => '9.0',
				'default' => tr('Based site long date and time setting'),
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_now($data, $params) 
{
	global $prefs;
	$default =  TikiLib::date_format($prefs['long_date_format'] . ' ' . $prefs['long_time_format']);
	if (!empty($params['format'])) {
		$ret = TikiLib::date_format($params['format']);
		//see if the user format setting results in a valid date, return default format if not
		try {
			$dateObj = new DateTime($ret);
		} catch (Exception $e) {
			return $default;
		}
		return $ret;
	} else {
		return $default;
	}
}
