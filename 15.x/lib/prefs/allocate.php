<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_allocate_list()
{
	$prefs = array(
		'unified_rebuild' => array('label' => tr('search index rebuild'), 'memory' => true, 'time' => true),
		'tracker_export_items' => array('label' => tr('tracker item export'), 'memory' => true, 'time' => true),
		'tracker_clear_items' => array('label' => tr('tracker clear'), 'memory' => false, 'time' => true),
	);

	$out = array();
	foreach ($prefs as $name => $info) {
		if ($info['memory']) {
			$out['allocate_memory_' . $name] = array(
				'name' => tr('Memory limit to apply during %0', $info['label']),
				'description' => tr('Temporarily adjust the memory limit to use during %0. Depending on the volume of data, some large operations require more memory. Increasing it locally, per operation, allows to keep a lower memory limit globally. Keep in mind that memory usage is still limited to what is available on the server.', $info['label']),
				'help' => 'Memory+Limit',
				'type' => 'text',
				'default' => '',
				'shorthint' => tr('for example: 256MB'),
				'size' => 8,
			);
		}

		if ($info['time']) {
			$out['allocate_time_' . $name] = array(
				'name' => tr('Time limit to apply during %0', $info['label']),
				'description' => tr('Temporarily adjust the time limit to use during %0. Depending on the volume of data, some requests may take longer. Increase the time limit locally to resolve the issue. Use reasonable values.', $info['label']),
				'help' => 'Time+Limit',
				'type' => 'text',
				'default' => '',
				'shorthint' => tr('seconds'),
				'size' => 8,
			);
		}
	}

	return $out;
}
