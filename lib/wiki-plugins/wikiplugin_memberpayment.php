<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_memberpayment_info() {
	global $prefs;

	return array(
		'name' => tra('Member Payment'),
		'documentation' => 'PluginMemberPayment',
		'description' => tra('Receive payments from a member to extend membership to a group.'),
		'validate' => 'all',
		'prefs' => array( 'wikiplugin_memberpayment', 'payment_feature' ),
		'icon' => 'pics/icons/money.png',
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group'),
				'description' => tra('Name of the group for which the subscription should be added or extended.'),
				'filter' => 'groupname',
				'default' => '',
			),
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tr('Price per period (%0).', $prefs['payment_currency']),
				'filter' => 'text',
			),
			'currentuser' => array(
				'required' => false,
				'name' => tra('Current User Member'),
				'description' => tra('Membership only for the current user'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'inputtitle' => array(
				'required' => false,
				'name' => tra('Input Title'),
				'description' => tra('Title of the input form.').' '. tra('Use %0 for the group name.').' '.tra('Supports wiki syntax'),
				'filter' => 'text',
				'default' => 'Membership to %0 for %1 (x%2)',
			),
			'howtitle' => array(
				'required' => false,
				'name' => tra('Tiki Payments Invoice Title'),
				'description' => tra('Title of the input form.').' '. tra('Use %0 for the group name, %4 for the number of days or %5 for the number of years').' '.tra('Supports wiki syntax'),
				'filter' => 'text',
				'default' => 'Membership to %0 for %1 (x%2)',
			),
			'preventdoublerequest' => array(
				'required' => false,
				'name' => tra('Prevent double request'),
				'description' => tra('Prevent user from extended if there is already a pending request'),
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

function wikiplugin_memberpayment( $data, $params, $offset ) {
	global $smarty, $userlib, $prefs, $user, $headerlib;
	global $paymentlib; require_once 'lib/payment/paymentlib.php';
	static $iPluginMemberpayment = 0;
	$attributelib = TikiLib::lib('attribute');

	$iPluginMemberpayment++;
	$smarty->assign('iPluginMemberpayment', $iPluginMemberpayment);
	$params['price'] = floatval( $params['price'] );
	$default = array( 'currentuser'=>'n', 'inputtitle'=>'', 'howtitle' => '');
	$params = array_merge( $default, $params );

	if ( ( $info = $userlib->get_group_info( $params['group'] ) ) && ( $info['expireAfter'] > 0 || $info['anniversary'] > '') ) {
		$smarty->assign( 'wp_member_offset', $offset );
		$smarty->assign( 'wp_member_price', $params['price'] );
		if (($info['expireAfter']/365)*365 == $info['expireAfter'] && $info['expireAfter'] >= 365) {
			$info['expireAfterYear'] = $info['expireAfter']/365;
		}
		$smarty->assign( 'wp_member_group', $info );
		$smarty->assign( 'wp_member_currentuser', $params['currentuser'] );
		
		if ($info['anniversary'] > '') {
			if (strlen($info['anniversary']) == 4) {
				$ann_month = substr($info['anniversary'],0,2);
				$ann_day = substr($info['anniversary'],2,2);
			} elseif (strlen($info['anniversary']) == 2) {
				$ann_month = 0;
				$ann_day = $info['anniversary'];
			}
			$smarty->assign( 'wp_member_anniversary_month', $ann_month );
			$smarty->assign( 'wp_member_anniversary_day', $ann_day );
			if ($params['currentuser'] == 'y') {
				$extend_until_info = $userlib->get_extend_until_info($user, $params['group']);
				if (!empty($extend_until_info['ratio_prorated_first_period']) && $extend_until_info['ratio_prorated_first_period'] < 1) {
					$smarty->assign( 'wp_member_prorated', round($extend_until_info['ratio_prorated_first_period'] * $params['price'], 2) );
				}	
			}
		}

		$smarty->assign('wp_member_requestpending', 'n');
        $smarty->assign('wp_member_paymentid', 0);
		if (isset($params['currentuser']) && $params['currentuser'] == 'y' && !empty($params['preventdoublerequest']) && $params['preventdoublerequest'] == 'y') {
			$attname = 'tiki.memberextend.' . $info['id'];
			$attributes = $attributelib->get_attributes('user', $user);
			if (isset($attributes[$attname])) {
				$smarty->assign('wp_member_requestpending', 'y');
				$smarty->assign('wp_member_paymentid', $attributes[$attname]);
			}
		}
		
		if ( isset($_POST['wp_member_offset']) && $_POST['wp_member_offset'] == $offset ) {
			$users = $params['currentuser'] == 'y'? array($user): explode( '|', $_POST['wp_member_users'] );
			$users = array_map( 'trim', $users );
			$users = array_filter( $users, array( $userlib, 'user_exists' ) );
			$users = array_filter( $users );

			if (!empty($params['preventdoublerequest']) && $params['preventdoublerequest'] == 'y') {
				foreach ($users as $u) {
					$attname = 'tiki.memberextend.' . $info['id']; 
					$attributes = $attributelib->get_attributes('user', $u);
					if (isset($attributes[$attname])) {
						return '{REMARKSBOX(type=warning, title=Plugin Memberpayment Error)}' . tra('The user ') . $u 
					. tra(' already has a pending extension request payment invoice ') . $attributes[$attname] . '{REMARKSBOX}';
					}
				}
			}
			$periods = max( 1, (int) $_POST['wp_member_periods'] );

			if ( count($users) == 1 ) {
				$desc = tr('Membership to %0 for %1 (x%2)', $params['group'], reset( $users ), $periods );
			} else {
				$desc = tr('Membership to %0 for %1 users (x%2)', $params['group'], count( $users ), $periods );
			}

			$cost = round( count($users) * $periods * $params['price'], 2 );
			// reduce cost due to prorated amount if applicable
			if ($info['anniversary'] > '') {
				foreach ($users as $u) {
					$extend_until_info = $userlib->get_extend_until_info($u, $params['group'], $periods);
					$cost = $cost - (1 - $extend_until_info['ratio_prorated_first_period']) * $params['price'];
				}
				$cost = round( $cost, 2 );
			}

			$id = $paymentlib->request_payment( $desc, $cost, $prefs['payment_default_delay'] );
			$paymentlib->register_behavior( $id, 'complete', 'extend_membership', array( $users, $params['group'], $periods, $info['id'] ) );

			foreach ($users as $u) {
				$attributelib->set_attribute('user', $u, 'tiki.memberextend.' . $info['id'], $id);
			}
			$paymentlib->register_behavior( $id, 'cancel', 'cancel_membership_extension', array( $users, $info['id'] ) );

			$smarty->assign( 'wp_member_title', $params['howtitle'] );
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $id ), $smarty ) . '~/np~^';
		} else if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && isset($_POST['invoice'])) {
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $_POST['invoice'] ), $smarty ) . '~/np~^';
		}

		$smarty->assign( 'wp_member_title', $params['inputtitle'] );
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

