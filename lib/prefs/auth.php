<?php

function prefs_auth_list() {
	return array(
		'auth_method' => array(
			'name' => tra('Authentication method'),
			'description' => tra('Multiple authentication backends are supported by Tikiwiki. The default value is to use the internal user database.'),
			'type' => 'list',
			'help' => 'Login+Authentication+Method',
			'options' => array(
				'tiki' => tra('Tiki'),
				'openid' => tra('Tiki and OpenID'),
				'pam' => tra('Tiki and PAM'),
				'ldap' => tra('Tiki and LDAP'),
				'cas' => tra('CAS (Central Authentication Service)'),
				'shib' => tra('Shibboleth'),
				'ws' => tra('Web Server'),
				'phpbb' => tra('phpBB'),
			),
		),
		'auth_token_access' => array(
			'name' => tra('Token Access'),
			'description' => tra('Allow to access the content with superior rights with the presentation of a token. The primary use of this authentication method is to grant temporary access to content to an external service.'),
			'type' => 'flag',
		),
		'auth_token_access_maxtimeout' => array(
			'name' => tra('Token Access Max Timeout'),
			'description' => tra('The maximum duration for which the generated tokens will be valid.'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);
}
