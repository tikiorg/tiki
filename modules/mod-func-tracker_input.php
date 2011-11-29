<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_tracker_input_info()
{
	return array(
		'name' => tra('Tracker Input'),
		'description' => tra('Allows to open a dialog to create new tracker items.'),
		'prefs' => array('feature_trackers'),
		'params' => array(
			'trackerId' => array(
				'name' => tr('Tracker'),
				'description' => tr('Tracker ID to render'),
				'filter' => 'int',
			),
			'textinput' => array(
				'name' => tr('Text Input'),
				'description' => tr('Multiple text fields to display as part of the main form along with the label. Field names map to the permanent names in the tracker field definitions. ex: groupName(Group Name) relatedTask(Task)'),
				'filter' => 'text',
			),
			'hiddeninput' => array(
				'name' => tr('Hidden Input'),
				'description' => tr('Hidden values to be sent over to the dialog. fieldName(value)'),
				'filter' => 'text',
			),
			'location' => array(
				'name' => tr('Location Field'),
				'description' => tr('Obtain the coordinates from a nearby map and send them to the location field.'),
				'filter' => 'text',
			),
		),
	);
}

function module_tracker_input($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$trackerId = $module_params['trackerId'];
	$itemObject = Tracker_Item::newItem($trackerId);

	if (! $itemObject->canModify()) {
		$smarty->assign('tracker_input', array(
			'trackerId' => 0,
			'textInput' => array(),
			'hiddenInput' => array(),
			'location' => null,
		));
		return;
	}

	$textinput = isset($module_params['textinput']) ? $module_params['textinput'] : '';
	$hiddeninput = isset($module_params['hiddeninput']) ? $module_params['hiddeninput'] : '';

	$location = null;
	if (isset($module_params['location'])) {
		$location = $module_params['location'];
		$hiddeninput .= " $location()";
	}

	preg_match_all("/(\w+)\(([^\)]+)\)/", $textinput, $parts, PREG_SET_ORDER);
	$text = array();
	foreach ($parts as $p) {
		$text[$p[1]] = tra($p[2]);
	}

	preg_match_all("/(\w+)\(([^\)]*)\)/", $hiddeninput, $parts, PREG_SET_ORDER);
	$hidden = array();
	foreach ($parts as $p) {
		$hidden[$p[1]] = $p[2];
	}

	$smarty->assign('tracker_input', array(
		'trackerId' => $trackerId,
		'textInput' => $text,
		'hiddenInput' => $hidden,
		'location' => $location,
	));
}

