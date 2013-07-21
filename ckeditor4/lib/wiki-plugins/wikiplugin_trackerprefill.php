<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerprefill_info()
{
	return array(
		'name' => tra('Tracker Prefill'),
		'documentation' => 'PluginTrackerPrefill',
		'description' => tra('Create a button to prefill tracker fields'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerprefill' ),
		'icon' => 'img/icons/application_form.png',
		'params' => array(
			'page' => array(
				'required' => true,
				'name' => tra('Page'),
				'description' => tra('Tracker Page Name'),
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'label' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => tra('Button Label.'),
				'default' => '',
			),
			'urlparams' => array(
				'required' => false,
				'name' => tra('URL Parameters'),
				'description' => tra('Parameters to pass in the url, e.g. &my_parameter1=123&my_parameter2=q'),
				'default' => '',
			),
			'field1' => array(
				'required' => true,
				'name' => tra('Field 1'),
				'description' => tra('Field ID for the first field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value1' => array(
				'required' => true,
				'name' => tra('Value 1'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field2' => array(
				'required' => false,
				'name' => tra('Field 2'),
				'description' => tra('Field ID for the second field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value2' => array(
				'required' => false,
				'name' => tra('Value 2'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field3' => array(
				'required' => false,
				'name' => tra('Field 3'),
				'description' => tra('Field ID for the third field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value3' => array(
				'required' => false,
				'name' => tra('Value 3'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field4' => array(
				'required' => false,
				'name' => tra('Field 4'),
				'description' => tra('Field ID for the fourth field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value4' => array(
				'required' => false,
				'name' => tra('Value 4'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field5' => array(
				'required' => false,
				'name' => tra('Field 5'),
				'description' => tra('Field ID for the fifth field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value5' => array(
				'required' => false,
				'name' => tra('Value 5'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field6' => array(
				'required' => false,
				'name' => tra('Field 6'),
				'description' => tra('Field ID for the sixth field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value6' => array(
				'required' => false,
				'name' => tra('Value 6'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field7' => array(
				'required' => false,
				'name' => tra('Field 7'),
				'description' => tra('Field ID for the seventh field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value7' => array(
				'required' => false,
				'name' => tra('Value 7'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),
			'field8' => array(
				'required' => false,
				'name' => tra('Field 8'),
				'description' => tra('Field ID for the eighth field'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'value8' => array(
				'required' => false,
				'name' => tra('Value 8'),
				'description' => tra('Content that should be used to prefill the field.'),
				'default' => '',
			),			
		),
	);
}

function wikiplugin_trackerprefill($data, $params)
{
	global $smarty;
	$prefills = array();
	foreach ($params as $param=>$value) {
		if (strstr($param, 'field')) {
			$id = substr($param, strlen('field'));
			$f['fieldId'] = $value;
			$f['value'] = $params["value$id"];
			$prefills[] = $f;
		}
	}
	$smarty->assign_by_ref('prefills', $prefills);
	$smarty->assign_by_ref('params', $params);
	return $smarty->fetch('wiki-plugins/wikiplugin_trackerprefill.tpl');
}
