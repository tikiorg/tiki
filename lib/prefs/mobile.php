<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_mobile_list()
{

	return array(

		'mobile_feature' => array(
			'name' => tra('Mobile access'),
			'description' => tra('Mobile feature pre-Tiki 13; as of Tiki 14 all it does is to allow automatic switching of the perspective according to the mobile_perspectives perefence.'),
			'help' => 'Mobile',
			'warning' => tra('Deprecated. This feature is no longer under development following the integration of the Bootstrap CSS framework.'),
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
