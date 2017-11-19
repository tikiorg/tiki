<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_menus_list()
{
	return [
		'menus_items_icons' => [
			'name' => tra('Menu icons'),
			'description' => tra('Allows icons to be defined for menu entries'),
			'type' => 'flag',
			'default' => 'n',
		],
		'menus_items_icons_path' => [
			'name' => tra('Default path for the icons'),
			'description' => tra(''),
			'type' => 'text',
			'default' => 'img/icons/large',
		],
		'menus_edit_icon' => [
			'name' => tra('Edit menu icon'),
			'description' => tra('Adds an icon on the navbar to edit menu entries'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
