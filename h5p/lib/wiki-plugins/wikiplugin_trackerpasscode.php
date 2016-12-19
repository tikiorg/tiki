<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerpasscode_info()
{
	return array(
		'name' => tra('Check Tracker Passcodes'),
		'documentation' => tra('PluginTrackerpasscode'),
		'description' => tra('Verify a tracker passcode'),
		'iconname' => 'permission',
		'introduced' => 7,
		'prefs' => array('wikiplugin_trackerpasscode', 'feature_trackers'),
		'filter' => 'wikicontent',
		'params' => array(
			'key' => array(
				'required' => true,
				'name' => tra('Session Key Names'),
				'description' => tra('Key name of passcode to be checked'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => '',
			),
			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Label of the key name of passcode to be checked'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => '',
			),
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Tracker from which to get passcode to check against'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'tracker',
			),
			'fieldId' => array(
				'required' => true,
				'name' => tra('Field ID'),
				'description' => tra('Field ID from which to get passcode to check against'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'tracker_field',
			),
			'itemId' => array(
				'required' => true,
				'name' => tra('Item ID'),
				'description' => tra('Item ID from which to get passcode to check against'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'tracker_item',
			),
		),
	);
}

function wikiplugin_trackerpasscode( $data, $params )
{
	global $user;
	$smarty = TikiLib::lib('smarty');

	if (empty($params['key']) || empty($params['trackerId']) || empty($params['itemId']) || empty($params['fieldId'])) {
		return '';
	}
	$key = $params['key'];
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['trackerpasscode'])) {
		// Check all filled in
		if (empty($_POST['trackerpasscode'])) {
			$access = TikiLib::lib('access');
			$access->redirect($_SERVER['REQUEST_URI'], tr('Please fill in all fields')); 
			die;
		}
		$_SESSION['wikiplugin_trackerpasscode'][$key] = $_POST['trackerpasscode']; 
	}
	$dataelse = '';
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}
	// check code
	$trklib = TikiLib::lib('trk');
	$correctcode = $trklib->get_item_value($params['trackerId'], $params['itemId'], $params['fieldId']);
	if ($_SESSION['wikiplugin_trackerpasscode'][$key] == $correctcode) {
		return $data;
	} else {
		$smarty->assign('label', $params['label']);
		$form = $smarty->fetch('wiki-plugins/wikiplugin_trackerpasscode.tpl');
		if (!empty($dataelse)) {
			return $dataelse . $form;
		} else {
			return $form;
		}
	}
} 
