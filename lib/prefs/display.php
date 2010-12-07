<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_display_list() {
	return array(
		'display_field_order' => array(
			'name' => tra('Fields display order'),
			'type' => 'list',
			'options' => array(
				'DMY' => tra('Day') . ' ' . tra('Month') . ' ' . tra('Year'),
				'DYM' => tra('Day') . ' ' . tra('Year') . ' ' . tra('Month'),
				'MDY' => tra('Month') . ' ' . tra('Day') . ' ' . tra('Year'),
				'MYD' => tra('Month') . ' ' . tra('Year') . ' ' . tra('Day'),
				'YDM' => tra('Year')  . ' ' . tra('Day') . ' ' . tra('Month'),
				'YMD' => tra('Year')  . ' ' . tra('Month') . ' ' . tra('Day'),
			),
		),
		'display_html_in_menus' => array(
			'name' => tra('Show HTML in menus'),
			'description' => tra('When set html tags in menu options will be "escaped" and displayed, otherwise option text will be rendered exactly as entered.'),
			'help' => 'Menus',
			'type' => 'flag',
		),
	);
}
