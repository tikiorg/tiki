<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_shopperinfo_info()
{
	return array(
		'name' => tra('Collect Anonymous Shopper Info'),
		'documentation' => tra('PluginShopperinfo'),
		'description' => tra('Collect shopper information for the shopping cart'),
		'prefs' => array('wikiplugin_shopperinfo', 'payment_feature'),
		'iconname' => 'cart',
		'introduced' => 7,
		'filter' => 'wikicontent',
		'format' => 'html',
		'tags' => array( 'experimental' ),
		'params' => array(
			'values' => array(
				'required' => true,
				'name' => tra('Values to be collected'),
				'description' => tra('Names of values to be collected separated by : as defined in shopper profile'),
				'since' => '7.0',
				'filter' => 'text',
				'separator' => ':'
			),
			'labels' => array(
				'required' => true,
				'name' => tra('Labels for the values to be collected'),
				'description' => tra('Labels of the values to be collected separated by a colon'),
				'since' => '7.0',
				'filter' => 'text',
				'separator' => ':'
			),
			'showifloggedin' => array(
				'required' => false,
				'name' => tra('Show even if logged in'),
				'description' => tra('Normally this is used for anonymous users but sometimes may be used when logged in also'),
				'since' => '7.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_shopperinfo( $data, $params )
{
	global $user;
	$smarty = TikiLib::lib('smarty');
	if ($user && $params['showifloggedin'] != 'y' || empty($params['values'])) {
		return '';
	}
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['shopperinfo'])) {
		foreach ($params['values'] as $v) {
			// Check all filled in
			if (empty($_POST[$v])) {
				$access = TikiLib::lib('access');
				$access->redirect($_SERVER['REQUEST_URI'], tr('Please fill in all fields')); 
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
