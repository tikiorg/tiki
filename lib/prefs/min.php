<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_min_list() {
	return array(
		'min_username_length' => array(
			'name' => tra('Minimum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
		'min_pass_length' => array(
			'name' => tra('Minimum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => 5,
		),
	);
}
	
