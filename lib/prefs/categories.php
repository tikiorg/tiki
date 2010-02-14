<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_categories_list() {
	return array(
	'categories_used_in_tpl' => array(
			'name' => tra('Categories used in templates (TPL)'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'feature_categories',
			),
		),
	);
}
