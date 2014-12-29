<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_mobile_list()
{

	return array(

		'mobile_feature' => array(
			'name' => tra('Mobile Access'),
			'description' => tra('Mobile feature pre Tiki 13, as of Tiki 14 all it does it to allow you to automatically switch perspective according to the mobile_perspectives perefence.'),
			'help' => 'Mobile',
			'warning' => tra('Deprecated. This feature is no longer under development following the switch to bootstrap.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
			'default' => 'n',
		),
		'mobile_perspectives' => array(
			'name' => tra('Mobile Perspectives'),
			'help' => 'Mobile',
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
			'dependencies' => array(
				'mobile_feature',
			),
			'default' => array(),
			'profile_reference' => 'perspective',
		),
	);
}
