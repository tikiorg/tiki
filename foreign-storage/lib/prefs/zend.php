<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_zend_list() {
	return array(
		'zend_mail_smtp_server' => array(
			'name' => tra('SMTP Server'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
			'default' => '',
		),
		'zend_mail_smtp_user' => array(
			'name' => tra('Username'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
			'parameters' => array(
				'autocomplete' => 'off'
			),
			'default' => '',
		),
		'zend_mail_smtp_pass' => array(
			'name' => tra('Password'),
			'type' => 'password',
			'size' => '20',
			'perspective' => false,
			'parameters' => array(
				'autocomplete' => 'off'
			),
			'default' => '',
		),
		'zend_mail_smtp_port' => array(
			'name' => tra('Port'),
			'type' => 'text',
			'size' => '5',
			'perspective' => false,
			'default' => 25,
		),
		'zend_mail_smtp_security' => array(
			'name' => tra('Security'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'' => tra('None'),
				'ssl' => tra('SSL'),
				'tls' => tra('TLS'),
			),
			'default' => '',
		),
		'zend_mail_handler' => array(
			'name' => tra('Mail Sender'),
			'type' => 'list',
			'options' => array(
				'sendmail' => tra('Sendmail'),
				'smtp' => tra('SMTP'),
			),
			'default' => 'sendmail',
		),
		'zend_mail_smtp_auth' => array(
			'name' => tra('Authentication'),
			'description' => tra('Mail server authentication'),
			'type' => 'list',
			'options' => array(
				'' => tra('None'),
				'login' => tra('LOGIN'),
				'plain' => tra('PLAIN'),
				'crammd5' => tra('CRAM-MD5'),
			),
			'default' => '',
		),
	);
}
