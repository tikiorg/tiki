<?php

function wikiplugin_shopperinfo_info() {
	return array(
		'name' => tra('Collect Anonymous Shopper Info'),
		'documentation' => tra('PluginShopperinfo'),
		'description' => tra('Collect Anonymous Shopper Info for Shopping Cart'),
		'prefs' => array(), // TODO
		'filter' => 'wikicontent',
		'format' => 'html',
		'params' => array(
			'values' => array(
				'required' => true,
				'name' => tra('Values to be collected'),
				'description' => tra('Names of values to be collected separated by : as defined in shopper profile'),
				'filter' => 'text',
				'default' => array(),
				'separator' => ':'
			),
			'labels' => array(
				'required' => true,
				'name' => tra('Labels for the values to be collected'),
				'description' => tra('Labels of the values to be collected separated by :'),
				'filter' => 'text',
				'default' => array(),
				'separator' => ':'
			),
			'showifloggedin' => array(
				'required' => false,
				'name' => tra('Show even if logged in'),
				'description' => tra('Normally this is used for anonymous users but sometimes may be used when logged in also'),
				'filter' => 'text',
				'default' => 'n',
			),
		),
	);
}

function wikiplugin_shopperinfo( $data, $params ) {
	global $smarty, $user, $access;
	if ($user && $params['showifloggedin'] != 'y' || empty($params['values'])) {
		return '';
	}
	if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['shopperinfo'])) {
		global $access;

		foreach ($params['values'] as $v) {
			// Check all filled in
			if (empty($_POST[$v])) {
				$access->redirect( $_SERVER['REQUEST_URI'], tr('Please fill in all fields') ); 
				die;
			}
		}
		foreach ($params['values'] as $v) {
			$_SESSION['shopperinfo'][$v] = $_POST[$v]; 
		}	
	}
	$values = array();	
	foreach ($params['values'] as $k => $v) {
		$t['name'] = $v; 
		if (!empty($_SESSION['shopperinfo'][$v])) {
			$t['current'] = $_SESSION['shopperinfo'][$v];
		}
		if (!empty($params['labels'][$k])) {
			$t['label'] = $params['labels'][$k];
		}
		$values[] = $t;
	}
	$smarty->assign('values', $values);
	$form = $smarty->fetch('wiki-plugins/wikiplugin_shopperinfo.tpl');
	return $form;	
} 
			
