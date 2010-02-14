<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
		),
		'zend_mail_smtp_user' => array(
			'name' => tra('Username'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
		),
		'zend_mail_smtp_pass' => array(
			'name' => tra('Password'),
			'type' => 'password',
			'size' => '20',
			'perspective' => false,
		),
		'zend_mail_smtp_port' => array(
			'name' => tra('Port'),
			'type' => 'text',
			'size' => '5',
			'perspective' => false,
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
		),
	);
}
