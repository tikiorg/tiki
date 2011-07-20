<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_contact_list() {
	return array (
		'contact_anon' => array(
			'name' => tra('Allow anonymous visitors to use the "Contact Us" feature.'),
			'description' => tra('Allow anonymous visitors to use the "Contact Us" feature.'),
			'type' => 'flag',
			'help' => 'Contact+us',
			'dependencies' => array(
				'feature_contact',
			),
			'default' => 'n',
		),
		'contact_user' => array(
			'name' => tra('Contact user'),
			'description' => tra('Contact user'),
			'type' => 'text',
			'size' => 40,
			'dependencies' => array(
				'feature_contact',
			),
			'default' => 'admin',
		),
	);
}
