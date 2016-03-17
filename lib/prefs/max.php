<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_max_list()
{
	return array(
		'max_username_length' => array(
			'name' => tra('Maximum length'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => 50,
		),
	);	
}
