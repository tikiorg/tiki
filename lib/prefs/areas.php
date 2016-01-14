<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_areas_list()
{
	return array(
		'areas_root' => array(
			'name' => tra('Areas root category ID'),
			'description' => tra('ID of category whose child categories are bound to a perspective by areas.'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'default' => 0,
			'help' => 'Areas',
			'profile_reference' => 'category',
			'dependencies' => array(
				'feature_areas',
			),
		),
	);
}

