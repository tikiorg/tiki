<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_cookie_info()
{
	return array(
		'name' => tra('Cookie'),
		'documentation' => 'PluginCookie',
		'description' => tra('Display a tagline or cookie'),
		'prefs' => array( 'wikiplugin_cookie' ),
		'iconname' => 'quotes',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_cookie( $data, $params )
{
	global $tikilib;

	// Replace cookie
	$cookie = $tikilib->pick_cookie();

	return $cookie;
}
