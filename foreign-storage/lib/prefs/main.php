<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_main_list() {
	return array(
		'main_shadow_start' => array(
			'name' => tra('Main shadow start'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'main_shadow_end' => array(
			'name' => tra('Main shadow end'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
