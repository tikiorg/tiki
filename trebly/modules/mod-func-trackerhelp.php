<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_trackerhelp_info() {
	return array(
		'name' => tra('Trackerhelp'),
		'description' => tra('Display the fieldId of a tracker'),
		'prefs' => array("feature_trackers"),
		'params' => array(
			'rows' => array(
				'name' => tra('Textarea rows'),
				'description' => tra('Textarea rows'),
				'filter' => 'int'
			),
			'cols' => array(
				'name' => tra('Textarea cols'),
				'description' => tra('Textarea cols'),
				'filter' => 'int'
			),
			'max' => array(
				'name' => tra('Max fields'),
				'description' => tra('Max fields'),
				'filter' => 'int'
			),
		)
	);
}

function module_trackerhelp( $mod_reference, &$module_params ) {
	global $smarty;
	$default = array('rows' => 4, 'cols' => 23, 'max'=> 100);
	$module_params = array_merge($default, $module_params);
	if (!empty($_REQUEST['trackerhelp'])) {
		global $trklib; include_once('lib/trackers/trackerlib.php');
		$trackerId = $trklib->get_tracker_by_name($_REQUEST['trackerhelp_name']);
		if (empty($trackerId)) {
			$tracker_info = $trklib->get_tracker($_REQUEST['trackerhelp_name']);
			if (!empty($tracker_info)) {
				$trackerId = $tracker_info['trackerId'];
				$_REQUEST['trackerhelp_name'] = $tracker_info['name'];
			}
		}
		if (!empty($trackerId)) {
			$objectperms = Perms::get(array('type' => 'tracker', 'object' => $trackerId));
		}
		if (empty($trackerId) || !$objectperms->view_trackers) {
			$_SESSION['trackerhelp_name'] = '';
			$_SESSION['trackerhelp_id'] = 0;
			$_SESSION['trackerhelp_text'] = array();
			$_SESSION['trackerhelp_pretty'] = array();
		} else {
			$_SESSION['trackerhelp_id'] = $trackerId;
			$_SESSION['trackerhelp_name'] = $_REQUEST['trackerhelp_name'];
			$fields = $trklib->list_tracker_fields($trackerId, 0, -1);
			$_SESSION['trackerhelp_text'] = array();
			$_SESSION['trackerhelp_pretty'] = array();
			foreach($fields['data'] as $field) {
				$_SESSION['trackerhelp_text'][] = $field['fieldId'].':'.$field['name'];
				$_SESSION['trackerhelp_pretty'][] = $field['name'].' {$f_'.$field['fieldId'].'}';
			}
		}
	}	
	if (!empty($_SESSION['trackerhelp_text']) && count($_SESSION['trackerhelp_text']) < $module_params['rows']) {
		$module_params['rows'] = count($_SESSION['trackerhelp_text']);
	}

	$smarty->assign('tpl_module_title', tra('Tracker Help'));
}
