<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_change_list()
{
	return array(
		'change_language' => array(
			'name' => tra('Users can change site language'),
			'description' => tra('Allow users to change the language of the menus and labels.'),
			'type' => 'flag',
			'default' => 'y',
			'tags' => array('basic'),
		),
		'change_theme' => array(
			'name' => tra('Users can change theme'),
			'description' => tra(''),
			'warning' => tra('Users can override the theme with this setting.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'change_password' => array(
			'name' => tra('Users can change their password'),
			'description' => tra('Allow users to change their own login password'),
			'type' => 'flag',
			'help' => 'User+Preferences',
			'default' => 'y',
			'tags' => array('basic'),
		),
	);
}
