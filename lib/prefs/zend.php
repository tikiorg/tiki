<?php

function prefs_zend_list() {
	return array(
		'zend_mail_smtp_server' => array(
			'name' => tra('SMTP Server'),
			'type' => 'text',
			'size' => '20',
		),
		'zend_mail_smtp_user' => array(
			'name' => tra('Username'),
			'type' => 'text',
			'size' => '20',
		),
		'zend_mail_smtp_pass' => array(
			'name' => tra('Password'),
			'type' => 'password',
			'size' => '20',
		),
		'zend_mail_smtp_port' => array(
			'name' => tra('Port'),
			'type' => 'text',
			'size' => '5',
		),
		'zend_mail_smtp_security' => array(
			'name' => tra('Security'),
			'type' => 'list',
			'options' => array(
				'' => tra('None'),
				'ssl' => tra('SSL'),
				'tls' => tra('TLS'),
			),
		),
		// Used in templates/tiki-admin-include-general.tpl
		'zend_mail_smtp_auth' => array(
			'name' => tra('Authentication'),
			'type' => 'list',
			'options' => array(
				'' => tra('None'),
				'login' => tra('LOGIN'),
				'plain' => tra('PLAIN'),
				'crammd5' => tra('CRAM-MD5'),
			),
		),
		// Used in templates/tiki-admin-include-general.tpl
		'zend_mail_handler' => array(
			'name' => tra('Mail Sender'),
			'type' => 'list',
			'options' => array(
				'sendmail' => tra('Sendmail'),
				'smtp' => tra('SMTP'),
			),
		),
	
	);
}
