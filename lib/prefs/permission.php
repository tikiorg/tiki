<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_permission_list() {
	return array(
		'permission_denied_url' => array(
			'name' => tra('Send to URL'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'permission_denied_login_box' => array(
			'name' => tra('On permission denied, display login module (for Anonymous)'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
