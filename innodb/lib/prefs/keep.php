<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_keep_list() {
	return array(
		'keep_versions' => array(
			'name' => tra('Never delete versions younger than'),
			'type' => 'text',
			'size' => '5',
			'shorthint' => tra('days'),
			'default' => 1,
		),
	);	
}
