<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_box_list()
{
	return array(
		'box_shadow_start' => array(
			'name' => tra('Module (box) shadow start'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'box_shadow_end' => array(
			'name' => tra('Module (box) shadow end'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
