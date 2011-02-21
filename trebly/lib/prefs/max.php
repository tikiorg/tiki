<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_max_list() {
	return array(
		'max_username_length' => array(
			'name' => tra('Maximum length'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
		),
	);	
}
