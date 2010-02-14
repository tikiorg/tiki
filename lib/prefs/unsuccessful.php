<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_unsuccessful_list() {
	return array(
		'unsuccessful_logins' => array(
			'name' => tra('Re-validate user by email after'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'shorthint' => tra('unsuccessful login attempts'),
			'hint' => tra('Use "-1" for never'),
		),
	);	
}
