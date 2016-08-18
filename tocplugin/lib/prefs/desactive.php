<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_desactive_list()
{
	return array(
		'desactive_login_autocomplete' => array(
			'name' => tra("Disable browser's autocomplete feature for username and password fields"),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
	);	
}
