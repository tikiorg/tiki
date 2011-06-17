<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_layout_list() {
	return array(
		'layout_section' => array(
			'name' => tra('Layout per section'),
			'type' => 'flag',
		),
		'layout_fixed_width' => array(
			'name' => tra('Layout Width'),
			'type' => 'text',
			'description' => tra('Constrains the site display width (default: 990px).'),
			'hint' => tra('ex.: 800px'),
			'dependencies' => array(
				'feature_fixed_width',
			),
		),
		'layout_tabs_optional' => array(
			'name' => tra('Users can choose not to have tabs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_tabs',
			),
		),
		'layout_add_body_group_class' => array(
			'name' => tra('Add group CSS info'),
			'hint' => tra('Add a class to the page BODY tag to indicate group membership'),
			'description' => tra('Either grp_Anonymous or grp_Registered and possibly grp_Admin as well'),
			'type' => 'flag',
		),
	);
}
