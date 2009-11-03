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
			),
		),
	);
}
