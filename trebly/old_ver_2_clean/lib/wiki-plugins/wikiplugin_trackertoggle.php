<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackertoggle_info() {
	return array(
		'name' => tra('trackertoggle'),
		'documentation' => 'PluginTrackertoggle',
		'description' => tra('Toggle element display on a field value'),
		'prefs' => array('feature_jquery', 'feature_trackers'),
		'params' => array(
			'fieldId' => array(
				'required' => true,
				'name' => tra('Field ID'),
				'description' => tra('Numeric value representing the field ID tested.'),
				'filter' => 'digits',
				'default' => '',
			),
			'value' => array(
				'required' => true,
				'name' => tra('Value'),
				'description' => tra('Value to compare against.'),
			),
			'visible' => array(
				'required' => false,
				'name' => tra('Element visibility'),
				'description' => 'y|n' . tra('If y, is visible when the field has the value.'),
				'default' => 'n'
			),
			'id' => array(
				'required' => true,
				'name' => tra('ID'),
				'description' => tra('Html ID of the element that is toggled'),
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Use the field of specific item. The URL param itemId is used if this parameter is not set.'),
			),
		),
	);
}

function wikiplugin_trackertoggle($data, $params) {
	global $trklib; include_once('lib/trackers/trackerlib.php');
	global $headerlib; include_once('lib/headerlib.php');
	$default = array('visible' => 'n');
	$params = array_merge($default, $params);
	extract($params,  EXTR_SKIP);
	$field = $trklib->get_tracker_field($fieldId);
	if (empty($field)) {
		return tra('Incorrect param');
	}
	if (empty($itemId) && !empty($_REQUEST['itemId'])) {
		$itemId = $_REQUEST['itemId'];
	}
	$htmlFieldId = "track_$fieldId";
	$action = $visible == 'y'? 'show': 'hide';
	$anti = $visible == 'y'? 'hide': 'show';
	if ($field['type'] == 'c') {
		$extension = ':checked';
		$value = $value == 'y'? 'undefined': "'on'";
	} else {
		$extension = '';
	}
	$htmltype = $field['type'] == 'a'? 'textarea': 'input';
	$jq = "if (\$('".$htmltype."[name=$htmlFieldId]$extension').val() == $value) {\$('#$id').$action();} else {\$('#$id').$anti();}";
	$jq .= "\$('".$htmltype."[name=$htmlFieldId]').change(function () { $jq });";

	$headerlib->add_jq_onready($jq);
}
