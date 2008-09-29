<?php

function wikiplugin_cookie_info()
{
	return array(
		'name' => tra('Cookie'),
		'description' => tra('?'),
		'prefs' => array( 'wikiplugin_cookie' ),
		'params' => array(
		),
	);
}

function wikiplugin_cookie( $data, $params )
{
	// Replace cookie
	$cookie = $this->pick_cookie();

	return $cookie;
}

?>
