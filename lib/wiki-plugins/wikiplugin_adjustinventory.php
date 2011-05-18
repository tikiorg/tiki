<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_adjustinventory_info() {
	return array(
		'name' => tra('Adjust Inventory'),
		'documentation' => tra('PluginAdjustInventory'),
		'description' => tra('Modifies inventory of a product'),
		'prefs' => array( 'wikiplugin_adjustinventory', 'payment_feature' ),
		'filter' => 'wikicontent',
		'format' => 'html',
		'params' => array(
			'code' => array(
				'required' => true,
				'name' => tra('Product ID'),
				'description' => tra('Product ID of item in the cart tracker'),
				'filter' => 'text',
				'default' => ''
			),
			'add' => array(
				'required' => false,
				'name' => tra('Show add'),
				'description' => tra('y|n'),
				'filter' => 'text',
				'default' => 'y',
			),
			'subtract' => array(
				'required' => false,
				'name' => tra('Show subtract'),
				'description' => tra('y|n'),
				'filter' => 'text',
				'default' => 'y',
			),
		),
	);
}

function wikiplugin_adjustinventory( $data, $params ) {
	if (!isset($params['add'])) {
		$params['add'] = 'y';
	}
	if (!isset($params['subtract'])) {
		$params['subtract'] = 'y';
	}
	global $smarty;
	$smarty->assign('code', $params['code']);
	$smarty->assign('add', $params['add']);
	$smarty->assign('subtract', $params['subtract']);
	$form = $smarty->fetch('wiki-plugins/wikiplugin_adjustinventory.tpl');

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost, $access;
		$add_quantity = $jitPost->add_quantity->int();
		$subtract_quantity = $jitPost->subtract_quantity->int();
		$quantity = $add_quantity - $subtract_quantity;
		if( $jitPost->code->text() == $params['code'] && $quantity != 0 ) {
			global $cartlib; require_once 'lib/payment/cartlib.php';
			$cartlib->change_inventory( $params['code'], $quantity );
		}
		$access->redirect( $_SERVER['REQUEST_URI'], tr('Inventory was adjusted by %0', $quantity ) );	
	}
	return $form;	
} 
			
