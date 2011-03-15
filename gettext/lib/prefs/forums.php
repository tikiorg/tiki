<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_forums_list() {
	return array(
		'forums_ordering' => array(
			'name' => tra('Default Ordering'),
			'type' => 'list',
			'options' => array(
				'created_asc' => tra('Creation Date (asc)'),
				'created_desc' => tra('Creation Date (desc)'),
				'threads_desc' => tra('Topics (desc)'),
				'comments_desc' => tra('Threads (desc)'),
				'lastPost_desc' => tra('Last post (desc)'),
				'hits_desc' => tra('Visits (desc)'),
				'name_desc' => tra('Name (desc)'),
				'name_asc' => tra('Name (asc)'),
			),
		),
	);
}
