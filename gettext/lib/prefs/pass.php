<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		'pass_chr_case' => array(
			'name' => tra('Require alphabetical characters in lower and upper case'),
			'type' => 'flag',
			'description' => tra('Password must contain at least one alphabetical character in lower case like a and one in upper case like A.'),
		),
		'pass_chr_special' => array(
			'name' => tra('Require special characters'),
			'type' => 'flag',
			'description' => tra('Password must contain at least one special character in lower case like " / $ % ? & * ( ) _ + ...'),
		),
		'pass_repetition' => array(
			'name' => tra('Require no consecutive repetition of the same character'),
			'type' => 'flag',
			'description' => tra('Password must contain no consecutive repetition of the same character as 111 or aab.'),
		),
		'pass_diff_username' => array(
			'name' => tra('Password must be different from the user login'),
			'type' => 'flag',
			'description' => tra('Password must be different from the user login.'),
		),	);	
}
