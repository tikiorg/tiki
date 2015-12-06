<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_generate_list()
{
	return array(
		'generate_password' => array(
			'name' => tra('Generate password'),
            'description' => tra('Include "Generate password" option in registration form'),
			'type' => 'flag',
			'default' => 'n',
		),
	);	
}
