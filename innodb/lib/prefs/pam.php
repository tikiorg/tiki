<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_pam_list() {
	return array(
		'pam_create_user_tiki' => array(
			'name' => tra('Create user if not in Tiki'),
			'type' => 'flag',
			'default' => 'n',
		),
		'pam_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
			'default' => 'n',
		),
		'pam_service' => array(
			'name' => tra('PAM service'),
			'type' => 'text',
			'size' => 20,
			'hint' => tra('Currently unused'),
			'default' => '',
		),
	);	
}
