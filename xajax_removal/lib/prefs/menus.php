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
			'name' => tra('Allow HTML in link text and do not escape the url in menu items (Please note: this might be insecure if you allow more people to edit menus)'),
			'description' => tra('If enabled, menu item names treated as HTML item content and they are not escaped (do not replace HTML special characters), this allows the use of HTML in menu items to insert images for example, and menu item urls are not escaped which allows the use of permissable href tags. Code must be valid. This allows menu item editors to put arbitrary HTML; only enable if you know what you are doing.'),
			'help' => 'Menus',
			'type' => 'flag',
		),
	);
}
