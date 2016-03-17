<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_header_list()
{
	return array(
		'header_shadow_start' => array(
			'name' => tra('HHeader shadow div start'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'header_shadow_end' => array(
			'name' => tra('Header shadow div end'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'header_custom_css' => array(
			'name' => tra('Custom CSS'),
			'description' => tra('Includes a custom block of CSS in the <html> <head> section of all pages.'),
			'type' => 'textarea',
			'size' => 5,
			'default' => '',
			'filter' => 'none',
		),
		'header_custom_js' => array(
			'name' => tra('Custom JavaScript'),
			'description' => tra('Includes a custom block of inline JavaScript in all pages.'),
			'type' => 'textarea',
			'size' => 5,
			'default' => '',
			'hint' => tra('Do not include the <script> and </script> tags.'),
			'filter' => 'none',
		),
		'header_custom_less' => array(
			'name' => tra('Custom Less'),
			'description' => tra('Compiles a new version of the currently selected theme and option using definitions declared here.'),
			'type' => 'textarea',
			'size' => 5,
			'default' => '',
			'hint' => tra('Custom Less (CSS precompiler) variable definitions'),
			'filter' => 'none',
		),
	);
}
