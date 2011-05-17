<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_cartmissinguserinfo_help() {
	return 'no documentation available at this time';
}

function wikiplugin_cartmissinguserinfo_info() {
	return array(
		'name' => tra('Cart Missing User Info'),
		'documentation' => tra('PluginCartMissingUserInfo'),		
		'description' => tra('Check if user still has missing info to enter'),
		'prefs' => array('wikiplugin_cartmissinguserinfo', 'feature_wiki'),
		'params' => array(
			'info_type' => array(
				'required' => true,
				'name' => tra('Type of Information'),
				'filter' => 'text',
				'default' => 'postpurchase',
				'options' => array(
					array('text' => tra('Post Purchase'), 'value' => 'postpurchase'),
					array('text' => tra('Required before purchase'), 'value' => 'required'),
				),
			),
			'product_class_id' => array(
				'required' => true,
				'name' => tra('Product Class ID'),
				'filter' => 'int',
				'default' => '', 
			),
		),
	);
}

function wikiplugin_cartmissinguserinfo($data, $params) {
	global $smarty;
	global $cartlib; require_once 'lib/payment/cartlib.php';	
	if (empty($params['product_class_id']) || empty($params['info_type'])) {
		return tra('Missing parameters');
	}
	$missinginfo = $cartlib->get_missing_user_information_fields( $params['product_class_id'], $params['info_type'] );


	//print_r($missinginfo);
	$formpage = $cartlib->get_missing_user_information_form( $params['product_class_id'], $params['info_type'] );
	$smarty->assign('cartmissinguserinfoform', $formpage);
	if (empty($missinginfo)) {
		$smarty->assign('cartmissinguserinfo', 'n');
		return $data;
	} else {
		$smarty->assign('cartmissinguserinfo', 'y');
		return '';
	}
}
