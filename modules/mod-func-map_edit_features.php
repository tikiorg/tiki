<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}


/**
 * @return array
 */
function module_map_edit_features_info()
{
	return array(
		'name' => tra('Map Feature Editor'),
		'description' => tra('Allows shapes to be drawn over the map.'),
		'prefs' => array(),
		'params' => array(
			'trackerId' => array(
				'name' => tr('Tracker'),
				'description' => tr('Tracker to store the feature in.'),
				'required' => true,
				'filter' => 'digits',
				'profile_reference' => 'tracker',
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
			'insertmode' => array(
				'name' => tr('Mode change on insert'),
				'description' => tr('Target mode to enter after successfully inserting an item'),
				'filter' => 'text',
				'default' => '',
			),
			'editdetail' => array(
				'name' => tr('Edit details'),
				'description' => tr("Edit the tracker item's details through a dialog after the initial item creation."),
				'filter' => 'int',
				'default' => 0,
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
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
			'editDetails' => isset($module_params['editdetail']) ? intval($module_params['editdetail']) : 0,
			'insertMode' => isset($module_params['insertmode']) ? $module_params['insertmode'] : '',
		)
	);
}

