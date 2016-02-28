<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_layout_list()
{
	return array(
		'layout_fixed_width' => array(
			'name' => tra('Layout width'),
            'description' => tra('Constrains the width of the content area of the site (default: 1170px).'),
			'type' => 'text',
			'hint' => tra('for example, 960px'),
			'dependencies' => array(
				'feature_fixed_width',
			),
			'default' => '',
		),
		'layout_tabs_optional' => array(
			'name' => tra('Tabs optional'),
            'description' => tra('Users can choose not to have tabs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_tabs',
			),
			'default' => 'y',
		),
		'layout_add_body_group_class' => array(
			'name' => tra('Add group CSS info'),
			'hint' => tra('Add CSS classes to the page <body> tag based on the user\'s group membership'),
			'description' => tra('Either grp_Anonymous or grp_Registered and possibly grp_Admins as well'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
