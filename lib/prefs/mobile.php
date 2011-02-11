<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_mobile_list() {

	return array(

		'mobile_feature' => array(
			'name' => tra('Mobile Access'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'warning' => tra('Experimental. This feature is under development.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_ajax',
			),
		),
		'mobile_perspectives' => array(
			'name' => tra('Mobile Perspectives'),
			'description' => tra('New mobile feature for Tiki 7'),
			'help' => 'Mobile',
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
			'dependencies' => array(
				'mobile_feature',
			),
		),
		
	);
}
