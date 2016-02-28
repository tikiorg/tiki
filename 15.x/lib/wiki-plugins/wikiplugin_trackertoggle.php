<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackertoggle_info()
{
	return array(
		'name' => tra('Tracker Toggle'),
		'documentation' => 'PluginTrackerToggle',
		'description' => tra('Show or hide tracker content'),
		'iconname' => 'trackers',
		'introduced' => 7,
		'prefs' => array('wikiplugin_trackertoggle', 'feature_jquery', 'feature_trackers'),
		'params' => array(
			'fieldId' => array(
				'required' => true,
				'name' => tra('Field ID'),
				'description' => tra('Numeric value representing the field ID tested.'),
				'since' => '7.0',
				'filter' => 'digits',
				'profile_reference' => 'tracker_field',
			),
			'value' => array(
				'required' => true,
				'name' => tra('Value'),
				'description' => tra('Value to compare against.'),
				'since' => '7.0',
				'filter' => 'text',
			),
			'visible' => array(
				'required' => false,
				'name' => tra('Element Visibility'),
				'description' => tra('Set whether visible when the field has the value.'),
				'since' => '7.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'id' => array(
				'required' => true,
				'name' => tra('ID'),
				'description' => tra('Html ID of the element that is toggled'),
				'since' => '7.0',
				'filter' => 'text',
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Use the field of specific item. The URL param itemId is used if this parameter
					is not set.'),
				'since' => '7.0',
				'filter' => 'digits',
				'default' => 0,
				'profile_reference' => 'tracker_item',
			),
		),
	);
}

function wikiplugin_trackertoggle($data, $params)
{
	$default = array('visible' => 'n');
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	if (empty($fieldId)) {
		TikiLib::lib('errorreport')->report(tr('trackertoggle: Param fieldId is required.'));
	}
	$field = TikiLib::lib('trk')->get_tracker_field($fieldId);
	if (empty($field)) {
		TikiLib::lib('errorreport')->report(tr('trackertoggle: Param fieldId field %0 does not exsist.', $fieldId));
	}

	if (!isset($value)) {
		TikiLib::lib('errorreport')->report(tr('trackertoggle: Param value is required'));
	}
	if (empty($id)) {
		TikiLib::lib('errorreport')->report(tr('trackertoggle: Param id is required'));
	}

	if (empty($itemId) && !empty($_REQUEST['itemId'])) {
		$itemId = $_REQUEST['itemId'];
	}

	$action = $visible == 'y'? 'show': 'hide';
	$anti = $visible == 'y'? 'hide': 'show';
	if (!empty($itemId)) {
		$fieldValue = TikiLib::lib('trk')->get_item_value(0, $itemId, $fieldId);
		if ($fieldValue === $value) {
			$jq = "\$('#$id').$action();";
		} else {
			$jq = "\$('#$id').$anti();";
		}
	} else {
		$htmlFieldName = "ins_$fieldId";

		$htmltype = $field['type'] == 'a'? 'textarea': 'input';
		$extension = '';
		$trigger = 'keyup';
		if (!is_numeric($value) && ! preg_match('/^[\'"].*[\'"]$/', $value)) {
			$value = "'$value'";		// add quotes if not already quoted and not a number
		}
		switch ($field['type']) {
			case 'c':
				$extension = ':checked';
				$value = $value == 'y'? 'undefined': "'on'";
				$trigger = 'change';
				break;
			case 'a':
				$htmltype = 'textarea';
				break;
			case 'd':
			case 'D':
			case 'w':
			case 'g':
			case 'u':
				$htmltype = 'select';
				$trigger = 'change';
				break;
			case 'R':
				$extension = ':checked';
				$htmltype = 'input';
				$trigger = 'change';
				break;
			case 'e':					// category - NB needs categId not categName to match
				if ($field['options_array'][1] == 'd') {
					$htmltype = 'select';
					$trigger = 'change';
					$htmlFieldName = "\"{$htmlFieldName}[]\"";
				}
				break;
			default:
		}
		$jq = "if (\$('".$htmltype."[name=$htmlFieldName]$extension').val() == $value) {\$('#$id').$action();} else {\$('#$id').$anti();}";
		$jq = "\$('".$htmltype."[name=$htmlFieldName]').$trigger(function () { $jq }).trigger('$trigger');";
	}

	TikiLib::lib('header')->add_jq_onready($jq);
}
