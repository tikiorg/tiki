<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_http_list()
{
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
			'description' => tra('The port used to access this server; if not specified, port 80 will be used'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => '',
			'shorthint' => tra('If not specified, port 80 will be used'),
		),
		'http_skip_frameset' => array(
			'name' => tra('HTTP lookup: skip framesets'),
			'description' => tra('When performing an HTTP request to an external source, verify if the result is a frameset and use heuristic to provide the real content.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'http_referer_registration_check' => array(
			'name' => tra('Registration referrer check'),
			'description' => tra('Use the HTTP referrer to check registration POST is sent from same host. (May not work on some setups.)'),
			'type' => 'flag',
			'default' => 'y',
		),
		'http_header_frame_options' => array(
			'name' => tra('HTTP Header X-Frame Options'),
			'description' => tra('The X-Frame-Options HTTP response header can be used to indicate whether or not a browser should be allowed to render a page in a &lt;frame&gt;, &lt;iframe&gt; or &lt;object&gt;'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_frame_options_value' => array(
			'name' => tra('Header Value'),
			'type' => 'list',
			'options' => array(
				'DENY' => tra('DENY'),
				'SAMEORIGIN' => tra('SAMEORIGIN'),
			),
			'default' => 'DENY',
			'perspective' => false,
			'tags' => array('basic'),
			'dependencies' => array(
				'http_header_frame_options',
			),
		),
		'http_header_xss_protection' => array(
			'name' => tra('HTTP Header X-XSS-Protection'),
			'description' => tra('The x-xss-protection header is designed to enable the cross-site scripting (XSS) filter built into modern web browsers'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_xss_protection_value' => array(
			'name' => tra('Header Value'),
			'type' => 'list',
			'options' => array(
				'0' => tra('0'),
				'1' => tra('1'),
				'1;mode=block' => tra('1;mode=block'),
			),
			'default' => '1;mode=block',
			'perspective' => false,
			'tags' => array('basic'),
			'dependencies' => array(
				'http_header_xss_protection',
			),
		),
		'http_header_content_type_options' => array(
			'name' => tra('HTTP Header X-Content-Type-Options'),
			'description' => tra('The x-content-type-options header is a marker used by the server to indicate that the MIME types advertised in the Content-Type headers should not be changed and be followed.'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_content_security_policy' => array(
			'name' => tra('HTTP Header Content-Security-Policy'),
			'description' => tra('The Content-Security-Policy header allows web site administrators to control resources the user agent is allowed to load for a given page.'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_content_security_policy_value' => array(
			'name' => tra('Header Value'),
			'type' => 'text',
			'default' => '',
			'perspective' => false,
			'tags' => array('basic'),
			'dependencies' => array(
				'http_header_content_security_policy',
			),
		),
		'http_header_strict_transport_security' => array(
			'name' => tra('HTTP Header Strict-Transport-Security'),
			'description' => tra('The Strict-Transport-Security header (often abbreviated as HSTS) is a security feature that lets a web site tell browsers that it should only be communicated with using HTTPS, instead of using HTTP.'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_strict_transport_security_value' => array(
			'name' => tra('Header Value'),
			'type' => 'text',
			'default' => '',
			'perspective' => false,
			'tags' => array('basic'),
			'dependencies' => array(
				'http_header_strict_transport_security',
			),
		),
		'http_header_public_key_pins' => array(
			'name' => tra('HTTP Header Public-Key-Pins'),
			'description' => tra('The Public-Key-Pins header associates a specific cryptographic public key with a certain web server to decrease the risk of MITM attacks with forged certificates. If one or several keys are pinned and none of them are used by the server, the browser will not accept the response as legitimate, and will not display it.'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'tags' => array('basic'),
		),
		'http_header_public_key_pins_value' => array(
			'name' => tra('Header Value'),
			'type' => 'textarea',
			'default' => '',
			'perspective' => false,
			'tags' => array('basic'),
			'dependencies' => array(
				'http_header_public_key_pins',
			),
		),
	);
}
