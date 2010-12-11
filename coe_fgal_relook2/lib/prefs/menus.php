<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_menus_list() {
	return array(
		'menus_items_icons' => array(
			'name' => tra('Menu icons'),
			'description' => tra('Allows to define icons for menu entries'),
			'type' => 'flag',
		),
		'menus_items_icons_path' => array(
			'name' => tra('Default path for the icons'),
			'type' => 'text',
		),
		'menus_item_names_raw' => array(
			'name' => tra('Allow HTML in menu items (Please note: this might be insecure if you allow more people to edit menus)'),
			'description' => tra('If enabled, treat menu item names as HTML item content and do not escape them (do not replace HTML special characters), allowing to use HTML in menu items to put images for example. Code must be valid. This allows menu item editors to put arbitrary HTML; only enable if you know what you are doing.'),
			'help' => 'Menus',
			'type' => 'flag',
		),
	);
}
