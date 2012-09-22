<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}


function module_map_edit_features_info()
{
	return array(
		'name' => tra('Map Feature Editor'),
		'description' => tra('Allows to draw shapes over the map.'),
		'prefs' => array(),
		'params' => array(
			'trackerId' => array(
				'name' => tr('Tracker'),
				'description' => tr('Tracker to store the feature in.'),
				'required' => true,
				'filter' => 'digits',
			),
			'hiddeninput' => array(
				'name' => tr('Hidden Input'),
				'description' => tr('Hidden values to be sent over to the dialog. fieldName(value)'),
				'filter' => 'text',
			),
			'standard' => array(
				'name' => tr('Standard controls'),
				'description' => tr('Dispay the standard draw controls'),
				'filter' => 'int',
				'default' => 1,
			),
		),
	);
}

function module_map_edit_features($mod_reference, $module_params)
{
	$targetField = null;
	$smarty = TikiLib::lib('smarty');

	$definition = Tracker_Definition::get($module_params['trackerId']);

	if ($definition) {
		foreach ($definition->getFields() as $field) {
			if ($field['type'] == 'GF') {
				$targetField = $field;
				break;
			}
		}
	}

	$hiddeninput = isset($module_params['hiddeninput']) ? $module_params['hiddeninput'] : '';
	preg_match_all('/(\w+)\(([^\)]*)\)/', $hiddeninput, $parts, PREG_SET_ORDER);
	$hidden = array();
	foreach ($parts as $p) {
		$hidden[$p[1]] = $p[2];
	}

	$smarty->assign(
		'edit_features',
		array(
			'trackerId' => $module_params['trackerId'],
			'definition' => $definition,
			'field' => $targetField,
			'hiddenInput' => $hidden,
			'standardControls' => isset($module_params['standard']) ? intval($module_params['standard']) : 1,
		)
	);
}

