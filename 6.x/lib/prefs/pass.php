<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_pass_list() {
	return array(
		'pass_chr_num' => array(
			'name' => tra('Require characters and numerals'),
			'type' => 'flag',
			'description' => tra('For improved security, require users to include a mix of characters and numerals in passwords.'),
		),
		'pass_due' => array(
			'name' => tra('Password expires after'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'shorthint' => tra('days'),
			'hint' => tra('Use "-1" for never'),
		),
	);	
}
