<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wizard_list()
{
	return array(
			'wizard_admin_hide_on_login' => array(
			'name' => tra('Hide admin wizard on login when an admin user logs in'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
	);			
}