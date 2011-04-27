<?php

function wikiplugin_hasticket_info() {
	return array(
		'name' => tra('Check if user has bought certain item or if it is in cart'),
		'documentation' => tra('PluginTrackerpasscode'),
		'description' => tra('Set'),
		'prefs' => array(), // TODO
		'filter' => 'wikicontent',
		'params' => array(
			'key' => array(
				'required' => true,
				'name' => tra('Session key names to be collected'),
				'description' => tra('Key name of passcode to be checked'),
				'filter' => 'text',
				'default' => '',
			),
			'label' => array(
				'required' => true,
				'name' => tra('Labels for the key names to be collected'),
				'description' => tra('Label of the key name of passcode to be checked'),
				'filter' => 'text',
				'default' => '',
			),
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Tracker from which to get passcode to check against'),
				'filter' => 'text',
				'default' => '',
			),
			'fieldId' => array(
				'required' => true,
				'name' => tra('Field ID'),
				'description' => tra('Field ID from which to get passcode to check against'),
				'filter' => 'text',
				'default' => '',
			),
			'itemId' => array(
				'required' => true,
				'name' => tra('Item ID'),
				'description' => tra('Item ID from which to get passcode to check against'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_hasticket( $data, $params ) {
	global $smarty, $user, $access;
	if (empty($params['key']) || empty($params['trackerId']) || empty($params['itemId']) || empty($params['fieldId'])) {
		return '';
	}
	$key = $params['key'];
	if( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['trackerpasscode'])) {
		global $access;

		// Check all filled in
		if (empty($_POST['trackerpasscode'])) {
			$access->redirect( $_SERVER['REQUEST_URI'], tr('Please fill in all fields') ); 
			die;
		}
		$_SESSION['wikiplugin_trackerpasscode'][$key] = $_POST['trackerpasscode']; 
	}
	$dataelse = '';
	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}
	// check code
	global $trklib; require_once("lib/trackers/trackerlib.php");
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
			
