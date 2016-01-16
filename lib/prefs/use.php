<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_use_list() 
{
	return array (
		'use_load_threshold' => array(
			'name' => tra('Close site when server load is above the threshold'),
			'description' => tra('Close the site when the server load is above the threshold (except for users with closed-site access permission)'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'use_proxy' => array(
			'name' => tra('Use proxy'),
			'description' => tra('Use proxy'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'use_context_menu_icon' => array(
			'name' => tra('Use context menus for actions (icons)'),
			'type' => 'flag',
			'default' => 'y',
		),
		'use_context_menu_text' => array(
			'name' => tra('Use context menus for actions (text)'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
