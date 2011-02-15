<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trade_info() {
	global $prefs;

	return array(
		'name' => tra('Trade'),
		'documentation' => tra('PluginTrade'),
		'description' => tra('Send or receive payments from one member to another. (for cclite only so far, experimental)'),
		'validate' => 'all',
		'prefs' => array( 'wikiplugin_trade', 'payment_feature' ),
		'params' => array(
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tr('Currency depends on the selected registry.'),
				'filter' => 'text',
				'default' => '',
			),
			'registry' => array(
				'required' => false,
				'name' => tra('Registry'),
				'description' => tr('Registry to trade in. Default: site preference (or first in list when more than one)'),
				'filter' => 'text',
				'default' => '',
			),
			'currency' => array(
				'required' => false,
				'name' => tra('Currency'),
				'description' => tr('Currency to trade in. Default: Cclite currency preference for registry set above'),
				'filter' => 'text',
				'default' => '',
			),
			'other_user' => array(
				'required' => false,
				'name' => tra('Other User'),
				'description' => tra('Name of the user to recieve or send the payment.') . ' ' . tra('Leave empty to display an input box.'),
				'filter' => 'username',
				'default' => '',
			),
			'wanted' => array(
				'required' => false,
				'name' => tra('Mode'),
				'description' => tr('Offered or wanted item.') . ' ' . tr('Default') . ':' . tra('Offered'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Offered'), 'value' => 'n'), 
					array('text' => tra('Wanted'), 'value' => 'y'), 
				),
				'filter' => 'alpha',
				'default' => 'n',
			),
			'action' => array(
				'required' => false,
				'name' => tra('Button Label'),
				'description' => tr('Default') . ':' . tra('Continue'),
				'filter' => 'text',
				'default' => tra('Continue'),
			),
			'inputtitle' => array(
				'required' => false,
				'name' => tra('Input Title'),
				'description' => tra('Title of the input form.').' '. tra('Use %0 for the amount, %1 for currency, %2 for your user name, %3 for the other user.').' '.tra('Supports wiki syntax') . '<br />'.
									tr('Default') . ':' . tra('"Payment of %0 %1 from user %2 to %3" for offered items, "Request payment of %0 %1 to user %2 from %3" for wanted'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_trade( $data, $params, $offset ) {
	global $smarty, $userlib, $prefs, $user, $headerlib;
	global $cclitelib; require_once 'lib/payment/cclitelib.php';
	global $paymentlib; require_once 'lib/payment/paymentlib.php';
	static $iPluginTrade = 0;

	$default = array( 'inputtitle'=>'', 'wanted' => 'n', 'action' => tra('Continue'), 'registry' => '', 'currency' => '' );
	$params = array_merge( $default, $params );
	
	if (empty($params['registry'])) {
		$params['registry'] = $cclitelib->get_registry();
	}
	if (empty($params['currency'])) {
		$params['currency'] = $cclitelib->get_currency($params['registry']);
	}
	
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
	$desc = tr($params['inputtitle'], number_format($params['price'], 2), $params['currency'], $user, $params['other_user'] );
	
	if( ( !empty($info) && $info['waiting'] == null )) {

		// user clicked "continue" (probably)
		if( isset($_POST['wp_trade_offset']) && $_POST['wp_trade_offset'] == $offset ) {

			$id = $paymentlib->request_payment( $desc, $params['price'], $prefs['payment_default_delay'], null, $params['currency'] );

			if (empty($user)) {
				return '{REMARKSBOX(type=warning, title=Plugin Trade Error)}' .
					tra('Please log in.') . '{REMARKSBOX}';
			} else {
				$params['main_user'] = $user;
			}
			$params['invoice'] = $id;
			$paymentlib->register_behavior( $id, 'complete', 'perform_trade', array($params) );

			//$smarty->assign( 'wp_trade_title', $desc );
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $id ), $smarty ) . '~/np~^';
		} else if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && isset($_POST['invoice'])) {
			require_once 'lib/smarty_tiki/function.payment.php';
			$params['id'] = $_POST['invoice'];
			return '^~np~' . smarty_function_payment( $params, $smarty ) . '~/np~^';
		}

	} else if ($info['waiting'] != null) {
		return '{REMARKSBOX(type=warning, title=Plugin Trade Error)}' . tra('The user ') . '<em>' . $info['login'] 
				. '</em>' . tra(' needs to validate their account.')
				. '{REMARKSBOX}';
	}
	
	$smarty->assign( 'wp_trade_title', $desc );
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_trade.tpl' ) . '~/np~';
}

