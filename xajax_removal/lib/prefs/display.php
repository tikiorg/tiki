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
			'name' => tra('Escape HTML in menu options'),
			'description' => tra('Use to "escape" or render HTML in menu options. If disabled, HTML will be allowed in menu options. Your code must be valid. If enabled, HTML code will be "escaped" and shown literally (as code) instead.'),
			'help' => 'Menus',
			'type' => 'flag',
		),
	);
}
