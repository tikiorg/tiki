<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_allocate_list()
{
	return array(
		'allocate_memory_unified_rebuild' => array(
			'name' => tra('Memory limit to apply during search index rebuild'),
			'description' => tra('Temporarily adjust the memory limit to use while rebuilding the index.'),
			'help' => 'Memory+Limit',
			'type' => 'text',
			'default' => '',
			'size' => 8,
		),
		'allocate_time_unified_rebuild' => array(
			'name' => tra('Time limit to apply during search index rebuild'),
			'description' => tra('Increase the amount of time allocated during index rebuild.'),
			'help' => 'Time+Limit',
			'type' => 'text',
			'default' => '',
			'size' => 8,
		),
	);
}
