<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_memberpayment_info() {
	global $prefs;

	return array(
		'name' => tra('Member Payment'),
		'description' => tra('Receive payments from a member and extend the length of the membership to a group.'),
		'validate' => 'all',
		'prefs' => array( 'wikiplugin_memberpayment', 'payment_feature' ),
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group'),
				'description' => tra('Name of the group for which the subscription should be added or extended.'),
				'filter' => 'groupname',
			),
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tr('Price per period (%0).', $prefs['payment_currency']),
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_memberpayment( $data, $params, $offset ) {
	global $smarty, $userlib, $prefs;
	global $paymentlib; require_once 'lib/payment/paymentlib.php';

	$params['price'] = floatval( $params['price'] );

	if( ( $info = $userlib->get_group_info( $params['group'] ) ) && $info['expireAfter'] > 0 ) {
		$smarty->assign( 'wp_member_offset', $offset );
		$smarty->assign( 'wp_member_price', $params['price'] );
		$smarty->assign( 'wp_member_group', $info );

		if( isset($_POST['wp_member_offset']) && $_POST['wp_member_offset'] == $offset ) {
			$users = explode( '|', $_POST['wp_member_users'] );
			$users = array_map( 'trim', $users );
			$users = array_filter( $users, array( $userlib, 'user_exists' ) );
			$users = array_filter( $users );

			$periods = max( 1, (int) $_POST['wp_member_periods'] );

			if( count($users) == 1 ) {
				$desc = tr('Membership to %0 for %1x%2', $params['group'], reset( $users ), $periods );
			} else {
				$desc = tr('Membership to %0 for %1 users (x%2)', $params['group'], count( $users ), $periods );
			}

			$cost = round( count($users) * $periods * $params['price'], 2 );

			$id = $paymentlib->request_payment( $desc, $cost, $prefs['payment_default_delay'] );
			$paymentlib->register_behavior( $id, 'complete', 'extend_membership', array( $users, $params['group'], $periods ) );

			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $id ), $smarty ) . '~/np~^';
		}

		return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_memberpayment.tpl' ) . '~/np~';
	} elseif ($info['expireAfter'] == 0 && $params['group'] == $info['groupName']) {
		return '{REMARKSBOX(type=warning, title=Plugin Memberpayment Error)}' . tra('The group ') . '<em>' . $info['groupName'] 
				. '</em>' . tra(' does not have a membership term.') . tra(' Go to ') . '<em>' . tra('Admin > Groups') . '</em>' 
				. tra(' to specify a term for this group by automatically unassigning users after a certain number of days.') 
				. '{REMARKSBOX}';
	} else {
		return '{REMARKSBOX(type=warning, title=Plugin Memberpayment Error)}' . tra('The group ') . '<em>' . $params['group'] 
				. '</em>' . tra(' does not exist') . '{REMARKSBOX}';
	}
}

