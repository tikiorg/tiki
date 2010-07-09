<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_trade.php  $

function wikiplugin_trade_info() {
	global $prefs;

	return array(
		'name' => tra('Trade'),
		'description' => tra('Send or receive payments from one member to another. (for cclite only so far, experimental)'),
		'validate' => 'all',
		'prefs' => array( 'wikiplugin_trade', 'payment_feature' ),
		'params' => array(
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tr('Price.', $prefs['payment_currency']),
				'filter' => 'text',
			),
//			'quantity' => array(
//				'required' => false,
//				'name' => tra('Quantity'),
//				'description' => tra('Number of items to trade.') . ' ' . tra('Leave empty to display an input box.') . ' ' . tra('Use negative numbers for input box default (otherwise default is 1).'),
//				'filter' => 'int',
//			),
			'other_user' => array(
				'required' => false,
				'name' => tra('Other user'),
				'description' => tra('Name of the user to recieve or send the payment.') . ' ' . tra('Leave empty to display an input box.'),
				'filter' => 'username',
			),
			'wanted' => array(
				'required' => false,
				'name' => tra('Mode'),
				'description' => tr('Offered or wanted item.') . ' ' . tr('Default') . ':' . tra('Offered'),
				'options' => array(
					array('text' => tra('Offered'), 'value' => 'n'), 
					array('text' => tra('Wanted'), 'value' => 'y'), 
				),
				'filter' => 'alpha',
			),
			'action' => array(
				'required' => false,
				'name' => tra('Button label'),
				'description' => tr('Default') . ':' . tra('Continue'),
				'filter' => 'text',
			),
			'inputtitle' => array(
				'required' => false,
				'name' => tra('Title of the input form.'),
				'description' => tra('Title of the input form.').' '. tra('Use %0 for the amount, %1 for currency, %2 for your user name, %3 for the other user.').' '.tra('Supports wiki syntax') . '<br />'.
									tr('Default') . ':' . tra('"Payment of %0 %1 from user %2 to %3" for offered items, "Request payment of %0 %1 to user %2 from %3" for wanted'),
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_trade( $data, $params, $offset ) {
	global $smarty, $userlib, $prefs, $user, $headerlib;
	global $paymentlib; require_once 'lib/payment/paymentlib.php';
	static $iPluginTrade = 0;

	$default = array( 'currentuser'=>'y', 'inputtitle'=>'', 'howtitle' => '', 'wanted' => 'n', 'action' => tra('Continue'), 'quantity' => -1);
	$params = array_merge( $default, $params );
	
	$iPluginTrade++;
	$smarty->assign('iPluginTrade', $iPluginTrade);
	
	$params['price'] = floatval( preg_replace('/^\D*([\d\.]*)/', '$1', $params['price'] ));
	$smarty->assign( 'wp_trade_other_user_set', empty($params['other_user']) ? 'n' : 'y' );
	$smarty->assign( 'wp_trade_action', $params['action']);
	
//	$smarty->assign( 'wp_trade_quantity_edit', $params['quantity'] <= 0 ? 'y' : 'n');	// TODO
//	$smarty->assign( 'wp_trade_quantity', abs($params['quantity']));
	
	if( isset($_POST['wp_trade_offset']) && $_POST['wp_trade_offset'] == $offset && !empty($_POST['wp_trade_other_user']) ) {
		$params['other_user'] = $_POST['wp_trade_other_user'];
	}
	$params['other_user'] = trim($params['other_user'], '|');
	$other_users = explode( '|', $params['other_user'] );
	$other_users = array_map( 'trim', $other_users );
	$other_users = array_filter( $other_users, array( $userlib, 'user_exists' ) );
	$other_users = array_filter( $other_users );
	
	if (!empty($other_users)) {
		$info = $userlib->get_user_info( $other_users[0] );
	} else {
		$info = array();
	}
	$smarty->assign( 'wp_trade_offset', $offset );
	$smarty->assign( 'wp_trade_price', $params['price'] );
	$smarty->assign( 'wp_trade_other_user', $info );
	$smarty->assign( 'wp_trade_currentuser', $params['currentuser'] );
	
	if( $params['wanted'] == 'n' ) {
		if (empty($params['inputtitle'])) {
			$params['inputtitle'] = 'Payment of %0 %1 from user %2 to %3';
		}
	} else {
		if (empty($params['inputtitle'])) {
			$params['inputtitle'] = 'Request payment of %0 %1 to user %2 from %3';
		}
	}
	$desc = tr($params['inputtitle'], number_format($params['price'], 2), $prefs['payment_currency'], $user, $params['other_user'] );
	
	if( ( !empty($info) && $info['waiting'] == null )) {

		if( isset($_POST['wp_trade_offset']) && $_POST['wp_trade_offset'] == $offset ) {

			$id = $paymentlib->request_payment( $desc, $params['price'], $prefs['payment_default_delay'] );
			$paymentlib->register_behavior( $id, 'complete', 'perform_trade', array( $user, $params['other_user'], $params['price'], $prefs['payment_currency'], $params['wanted'] ) );

			//$smarty->assign( 'wp_trade_title', $desc );
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $id ), $smarty ) . '~/np~^';
		} else if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && isset($_POST['invoice'])) {
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $_POST['invoice'] ), $smarty ) . '~/np~^';
		}

	} elseif ($info['waiting'] != null) {
		return '{REMARKSBOX(type=warning, title=Plugin Trade Error)}' . tra('The user ') . '<em>' . $info['login'] 
				. '</em>' . tra(' needs to validate their account.')
				. '{REMARKSBOX}';
	}
	
	$smarty->assign( 'wp_trade_title', $desc );
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_trade.tpl' ) . '~/np~';
}

