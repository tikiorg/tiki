<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_lock_list()
{
	return array (
		'lock_content_templates' => array(
			'name' => tra('Lock Content Templates'),
            'description' => tra('Enable users to lock content templates and so prevent others from editing them'),
			'type' => 'flag',
			'default' => 'n',
		),
		'lock_wiki_structures' => array(
			'name' => tra('Lock Wiki Structures'),
            'description' => tra('Enable users to lock wiki structures and so prevent others from editing them'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
