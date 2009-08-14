<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerprefill.php,v 1.1.2.1 2008-02-04 14:01:05 sylvieg Exp $
function wikiplugin_trackerprefill_help() {
	$help = tra('Displays a button to link to a page with a tracker plugin with prefilled tracker fields.');
	$help .= '~np~{TRACKERPREFILL(page=trackerpage,label=text,field1=id,value1=, field2=id,value2=... /)}';
	return $help;
}

function wikiplugin_trackerprefill_info() {
	return array(
		'name' => tra('Tracker Prefill'),
		'documentation' => 'PluginTrackerPrefill',
		'description' => tra('Displays a button to link to a page with a tracker plugin with prefilled tracker fields.'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerprefill' ),
		'params' => array(
			'page' => array(
				'required' => true,
				'name' => tra('Page'),
				'description' => tra('Tracker page name'),
			),
			'label' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => tra('Button label.'),
			),
			'field1' => array(
				'required' => true,
				'name' => tra('Field 1'),
				'description' => tra('Field ID'),
			),
			'value1' => array(
				'required' => true,
				'name' => tra('Value 1'),
				'description' => tra('Content of the field.'),
			),
			'field2' => array(
				'required' => false,
				'name' => tra('Field 2'),
				'description' => tra('Field ID'),
			),
			'value2' => array(
				'required' => false,
				'name' => tra('Value 2'),
				'description' => tra('Content of the field.'),
			),
			'field3' => array(
				'required' => false,
				'name' => tra('Field 3'),
				'description' => tra('Field ID'),
			),
			'value3' => array(
				'required' => false,
				'name' => tra('Value 3'),
				'description' => tra('Content of the field.'),
			),
			'field4' => array(
				'required' => false,
				'name' => tra('Field 4'),
				'description' => tra('Field ID'),
			),
			'value4' => array(
				'required' => false,
				'name' => tra('Value 4'),
				'description' => tra('Content of the field.'),
			),
			'field5' => array(
				'required' => false,
				'name' => tra('Field 5'),
				'description' => tra('Field ID'),
			),
			'value5' => array(
				'required' => false,
				'name' => tra('Value 5'),
				'description' => tra('Content of the field.'),
			),
		),
	);
}

function wikiplugin_trackerprefill($data, $params) {
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
