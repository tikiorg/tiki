<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_module($params, $smarty)
{
	static $instance = 0;

	$instance++;
	if (empty($params['moduleId'])) {
		$moduleId = 'wikiplugin_' . $instance;
	} else {
		$moduleId = $params['moduleId'];
	}

	if (empty($params['module'])) {
		return tr("Missing %0 parameter", 'module');
	}

	$module_reference = array(
		'moduleId' => $moduleId,
		'name' => $params['module'],
		'params' => $params,
		'rows' => 10,
		'position' => null,
		'ord' => null,
		'cache_time'=> 0,
	);

	foreach (array('module_style', 'rows') as $key) {
		if (!empty($params[$key])) {
			$module_reference[$key] = $params[$key];
		}
	}

	$modlib = TikiLib::lib('mod');
	return $modlib->execute_module($module_reference);
}
