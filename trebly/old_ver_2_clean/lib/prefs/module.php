<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_module_list() {
	return array(
		'module_zones_top' => array(
			'name' => tra('Top module zone'),
			'description' => tra('Visibility of area to keep modules such as logo, login etc (header)'),
			'type' => 'list',
			'keywords' => tra('side bar'),
			'help' => 'Users+Flip+Columns',
			'options' => array(
				'y' => tra('Only if module'),
				'fixed' => tra('Always'),
//				'user' => tra('User Decides'),
				'n' => tra('Never'),
			),
		),
		'module_zones_topbar' => array(
			'name' => tra('Topbar module zone'),
			'description' => tra('Visibility of area for modules such as main horizontal menu, search form, page-wide content, etc.'),
			'type' => 'list',
			'keywords' => tra('topbar'),
			'help' => 'Users+Flip+Columns',
			'options' => array(
				'y' => tra('Only if module'),
				'fixed' => tra('Always'),
//				'user' => tra('User Decides'),
				'n' => tra('Never'),
			),
		),
		'module_zones_pagetop' => array(
			'name' => tra('Page top module zone'),
			'description' => tra('Visibility of area to keep modules such as share etc'),
			'type' => 'list',
			'keywords' => tra('side bar'),
			'help' => 'Users+Flip+Columns',
			'options' => array(
				'y' => tra('Only if module'),
				'fixed' => tra('Always'),
//				'user' => tra('User Decides'),
				'n' => tra('Never'),
			),
		),
		'module_zones_bottom' => array(
			'name' => tra('Bottom module zone'),
			'description' => tra('Visibility of area to keep modules such as "powered by" and "rss list" (footer)'),
			'type' => 'list',
			'keywords' => tra('side bar'),
			'help' => 'Users+Flip+Columns',
			'options' => array(
				'y' => tra('Only if module'),
				'fixed' => tra('Always'),
//				'user' => tra('User Decides'),
				'n' => tra('Never'),
			),
		),
		'module_zones_pagebottom' => array(
			'name' => tra('Page bottom module zone'),
			'description' => tra('Visibility of area to keep modules at the foot of each page'),
			'type' => 'list',
			'keywords' => tra('side bar'),
			'help' => 'Users+Flip+Columns',
			'options' => array(
				'y' => tra('Only if module'),
				'fixed' => tra('Always'),
//				'user' => tra('User Decides'),
				'n' => tra('Never'),
			),
		),
	);
}
