<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_categorypath_list()
{
	return array(
		'categorypath_excluded' => array(
			'name' => tra('Exclude these category IDs'),
			'description' => tra('Category path won\'t appear for these category IDs.'),
			'separator' => ',',
			'type' => 'text',
			'size' => '15',
			'default' => array(''), //empty string needed to keep preference from setting unexpectedly
		),
	);
}
