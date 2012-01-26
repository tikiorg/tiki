<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_cookie.php 36213 2011-08-16 00:55:32Z marclaporte $

function wikiplugin_cookie_info()
{
	return array(
		'name' => tra('Cookie'),
		'documentation' => 'PluginCookie',
		'description' => tra('Display a rotating set of taglines or quotes (also known as fortune cookies)'),
		'prefs' => array( 'wikiplugin_cookie' ),
		'icon' => 'pics/icons/quotes.png',
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
