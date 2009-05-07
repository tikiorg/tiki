<?php

function wikiplugin_cookie_info()
{
	return array(
		'name' => tra('Cookie'),
		'documentation' => 'PluginCookie',
		'description' => tra('Also known as Fortune cookies or Taglines'),
		'prefs' => array( 'wikiplugin_cookie' ),
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

?>
