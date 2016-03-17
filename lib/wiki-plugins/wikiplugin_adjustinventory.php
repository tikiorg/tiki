<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_adjustinventory_info()
{
	return array(
		'name' => tra('Adjust Inventory'),
		'documentation' => tra('PluginAdjustInventory'),
		'description' => tra('Adjust the inventory level of a product'),
		'prefs' => array( 'wikiplugin_adjustinventory', 'payment_feature' ),
		'filter' => 'wikicontent',
		'introduced' => 7,
		'format' => 'html',
		'iconname' => 'add',
		'tags' => array( 'experimental' ),
		'params' => array(
			'code' => array(
				'required' => true,
				'name' => tra('Product ID'),
				'description' => tra('Product ID of item in the cart tracker'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'add' => array(
				'required' => false,
				'name' => tra('Show Add'),
				'description' => tra('Show option to add to inventory'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'subtract' => array(
				'required' => false,
				'name' => tra('Show Subtract'),
				'description' => tra('Show option to subtract from inventory'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
	);
}

function wikiplugin_adjustinventory( $data, $params )
{
	if (!isset($params['add'])) {
		$params['add'] = 'y';
	}
	if (!isset($params['subtract'])) {
		$params['subtract'] = 'y';
	}
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('code', $params['code']);
	$smarty->assign('add', $params['add']);
	$smarty->assign('subtract', $params['subtract']);
	$form = $smarty->fetch('wiki-plugins/wikiplugin_adjustinventory.tpl');

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost;
		$access = TikiLib::lib('access');
		$add_quantity = $jitPost->add_quantity->int();
		$subtract_quantity = $jitPost->subtract_quantity->int();
		$quantity = $add_quantity - $subtract_quantity;
		if ( $jitPost->code->text() == $params['code'] && $quantity != 0 ) {
			$cartlib = TikiLib::lib('cart');
			$cartlib->change_inventory($params['code'], $quantity);
		}
		$access->redirect($_SERVER['REQUEST_URI'], tr('Inventory was adjusted by %0', $quantity));	
	}
	return $form;	
} 
			
