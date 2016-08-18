<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_memberpayment_info()
{
	global $prefs;

	return array(
		'name' => tra('Member Payment'),
		'documentation' => 'PluginMemberPayment',
		'description' => tra('Receive payment from a member to extend membership to a group'),
		'validate' => 'all',
		'prefs' => array( 'wikiplugin_memberpayment', 'payment_feature' ),
		'iconname' => 'money',
		'introduced' => 5,
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group'),
				'description' => tra('Name of the group for which the subscription should be added or extended.'),
				'since' => '5.0',
				'filter' => 'groupname',
				'default' => '',
			),
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tr('Price per period (%0).', $prefs['payment_currency']),
				'since' => '5.0',
				'filter' => 'text',
			),
			'currentuser' => array(
				'required' => false,
				'name' => tra('Current User Member'),
				'description' => tra('Membership only for the current user'),
				'since' => '6.0',
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
				'description' => tra('Title of the initial input form.').' '. tr('Use %0 for the group name, %1 for
					the price, %2 for the currency, %4 for the number of days and %5 for the number of years.')
					. ' ' . tra('Supports wiki syntax.'),
				'since' => '6.0',
				'filter' => 'text',
				'default' => 'Membership to %0 for %1 (x%2)',
			),
			'inputtitleonly' => array(
				'required' => false,
				'name' => tra('Input Title Only'),
				'description' => tr('Select Yes (%0y%1) to just show the title of the input form and not the period and
					cost information. Input Title must be set as well.', '<code>', '</code>'),
				'since' => '11.0',
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				)
			),
			'howtitle' => array(
				'required' => false,
				'name' => tra('Initial Payment Form Title'),
				'description' => tra('Add a title to the payment form when initially shown after clicking "Continue".')
					. ' ' . tra('Use %0 for the group name, %1 for the price, %2 for the currency, %4 for the number
					of days and %5 for the number of years.') . ' ' . tra('Supports wiki syntax'),
				'since' => '6.0',
				'filter' => 'text',
				'default' => 'Membership to %0 for %1 (x%2)',
			),
			'howtitleonly' => array(
				'required' => false,
				'name' => tra('Payment Form Title Only'),
				'description' => tr('Select Yes (%0y%1) to just show the title of the payment form. Initial Payment Form
					Title must be set as well.', '<code>', '</code>'),
				'since' => '11.0',
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				)
			),
			'paytitle' => array(
				'required' => false,
				'name' => tra('Subsequent Payment Form Title'),
				'description' => tra('Title of the payment form after the initial showing.') . ' ' .
					tra('Use %0 for the group name, %1 for the price, %2 for the currency, %4 for the number of days
						and %5 for the number of years.') . ' ' . tra('Supports wiki syntax'),
				'since' => '11.0',
				'filter' => 'text',
				'default' => 'Membership to %0 for %1 (x%2)',
			),
			'paytitleonly' => array(
				'required' => false,
				'name' => tra('Subsequent Payment Form Title Only'),
				'description' => tr('Select Yes (%0y%1) to just show the title of the payment form that shows after the
					initial viewing. Subsequent Payment Form Title must be set as well.', '<code>', '</code>'),
				'since' => '11.0',
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				)
			),
			'preventdoublerequest' => array(
				'required' => false,
				'name' => tra('Prevent Double Request'),
				'description' => tra('Prevent user from extended if there is already a pending request'),
				'since' => '8.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'freeperiods' => array(
				'required' => false,
				'name' => tra('Free Periods'),
				'description' => tra('Give specified numbers of free periods, the first one could be prorated, in
					addition to those bought'),
				'since' => '9.0',
				'filter' => 'int',
				'default' => 0,
			),
			'hideperiod' => array(
				'required' => false,
				'name' => tra('Hide Period'),
				'description' => tra('Do not allow user to set period - use default of 1.'),
				'since' => '11.0',
				'filter' => 'alpha',
				'default' => 'n',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'periodslabel' => array(
				'required' => false,
				'name' => tra('Periods Label'),
				'description' => tr('Customize the label for the periods input. No effect if Hide Period is set to
					Yes (%0y%1).', '<code>', '</code>'),
				'since' => '9.1',
				'filter' => 'text',
				'default' => 'Number of periods:',
			),
			'returnurl' => array(
				'required' => false,
				'name' => tra('Return URL'),
				'description' => tra('Page that payment service returns to after processing.'),
				'since' => '11.0',
				'filter' => 'url',
				'default' => '',
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_memberpayment( $data, $params, $offset )
{
	global $prefs, $user;
	static $iPluginMemberpayment = 0;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');

	$iPluginMemberpayment++;
	$smarty->assign('iPluginMemberpayment', $iPluginMemberpayment);
	$smarty->assign('returnurl', !empty($params['returnurl']) ? $params['returnurl'] : '');
	$params['price'] = floatval($params['price']);
	$default = array( 'currentuser'=>'n', 'inputtitle'=>'', 'inputtitleonly'=>'n', 'howtitle' => '',
					'howtitleonly' => 'n', 'paytitle' => '', 'paytitleonly' => 'n', 'hideperiod' => 'n',
					'periodslabel' => 'Number of periods:');
	$params = array_merge($default, $params);
	$smarty->assign('hideperiod', $params['hideperiod']);
	$smarty->assign('periodslabel', $params['periodslabel']);
	//true if continue button has been hit
	$post = isset($_POST['wp_member_offset']);
	$oneuser = false;

	if ( ( $info = $userlib->get_group_info($params['group']) ) && ( $info['expireAfter'] > 0 || $info['anniversary'] > '') ) {
		$attributelib = TikiLib::lib('attribute');
		$paymentlib = TikiLib::lib('payment');
		$tikilib = TikiLib::lib('tiki');
		$smarty->assign('wp_member_offset', $offset);
		$smarty->assign('wp_member_price', $params['price']);

		if ($post) {
			$periods = (int) $_POST['wp_member_periods'];
			$freeperiods = 0;
			if ($periods && !empty($params['freeperiods'])) {
				// give free periods (purchase of at least 1 full real period required)
				$freeperiods = (int) $params['freeperiods'];
				$periods += $freeperiods;
			}
			$smarty->assign('wp_member_postperiods', $periods);
			$oneuser = $params['currentuser'] == 'y' ? array($user) : explode('|', $_POST['wp_member_users']);
			$oneuser = count($oneuser) == 1 ? true : false;
			if ($oneuser) {
				$extendinfo = $userlib->get_extend_until_info($user, $params['group'], $periods);
				$paidterm = $extendinfo['interval'];
			}
		}

		if (!empty($info['expireAfter'])) {
			$smarty->assign('wp_member_expireafter', true);
			if ($info['expireAfter'] == 1) {
				$days = tra('day');
			} elseif ($info['expireAfter'] > 1) {
				$days = tra('days');
			}
			$info['termString'] = $info['expireAfter'] . ' ' . $days;
			//set up subscription parameters for paypal - interval (D, M or Y) and number of intervals are required
			if ($prefs['payment_system'] == 'paypal' && $oneuser) {
				if ($paidterm->y > 2 || ($paidterm->y <= 2 && $paidterm->y > 0 && $paidterm->m == 0)) {
					$ppinterval = 'Y';
					$ppunits = $paidterm->y;
				//maximum number of days that can be specified in paypal is 90 for subscriptions
				} elseif ($paidterm->days > 90) {
					$ppinterval = 'M';
					$ppunits = $paidterm->m + ($paidterm->y * 12);
				} else {
					$ppinterval = 'D';
					$ppunits = $paidterm->days;
				}
				$beg = isset($extendinfo['new']) && $extendinfo['new'] === false ? tra('by') : tra('for');
				$info['descString'] = $beg . ' ' . $info['expireAfter'] * $periods . ' ' . $days;
			}
		}
		$smarty->assign('wp_member_prorated', 0); // default
		
		if ($info['anniversary'] > '') {
			if (strlen($info['anniversary']) == 4) {
				$ann_month = substr($info['anniversary'], 0, 2);
				$ann_day = substr($info['anniversary'], 2, 2);
				$ppinterval = 'Y';
				$fakedate = DateTime::createFromFormat('m-d', $ann_month . '-' . $ann_day);
				$info['termString'] = tr('Annual, commencing %0 %1 each year', tra($fakedate->format('M')),
					$fakedate->format('j'));
			} elseif (strlen($info['anniversary']) == 2) {
				$ann_month = 0;
				$ann_day = $info['anniversary'];
				$ppinterval = 'M';
				$info['termString'] = tr('Monthly, commencing on day %0 each month', $ann_day);
			}
			if ($oneuser) {
				$info['descString'] = 'to ' . $tikilib->get_short_date($extendinfo['timestamp'], $user);
			}
			$smarty->assign('wp_member_anniversary_month', $ann_month);
			$smarty->assign('wp_member_anniversary_day', $ann_day);
			if ($params['currentuser'] == 'y') {
				$extend_until_info = $userlib->get_extend_until_info($user, $params['group']);
				if (!empty($extend_until_info['ratio_prorated_first_period'])
					&& $extend_until_info['ratio_prorated_first_period'] > 0
					&& $extend_until_info['ratio_prorated_first_period'] < 1)
				{
					$smarty->assign('wp_member_prorated', round($extend_until_info['ratio_prorated_first_period']
						* $params['price'], 2));
				}
			}
		}
		$smarty->assign('wp_member_group', $info);
		$smarty->assign('wp_member_currentuser', $params['currentuser']);

		// setup free period display
		if (!empty($params['freeperiods'])) {
			if (isset($extendinfo['ratio_prorated_first_period'])
				&& $extendinfo['ratio_prorated_first_period'] > 0
				&& $extendinfo['ratio_prorated_first_period'] < 1)
			{
				$smarty->assign('wp_member_freeperiods', $params['freeperiods'] - 1);
				$smarty->assign('wp_member_freeprorated', 1);
			} else {
				$smarty->assign('wp_member_freeperiods', $params['freeperiods']);
				$smarty->assign('wp_member_freeprorated', 0);
			}
		} else {
			$smarty->assign('wp_member_freeperiods', 0);
			$smarty->assign('wp_member_freeprorated', 0);
		}
		
		$smarty->assign('wp_member_requestpending', 'n');
		$smarty->assign('wp_member_title', $params['inputtitle']);
		$smarty->assign('wp_member_titleonly', $params['inputtitleonly']);
        $smarty->assign('wp_member_paymentid', 0);
		if (isset($params['currentuser']) && $params['currentuser'] == 'y' && !empty($params['preventdoublerequest']) && $params['preventdoublerequest'] == 'y') {
			$attname = 'tiki.memberextend.' . $info['id'];
			$attributes = $attributelib->get_attributes('user', $user);
			if (isset($attributes[$attname])) {
				$smarty->assign('wp_member_requestpending', 'y');
				$smarty->assign('wp_member_paymentid', $attributes[$attname]);
				if (!empty ($params['paytitle'])) {
					$smarty->assign('wp_member_title', $params['paytitle']);
					$smarty->assign('wp_member_titleonly', $params['paytitleonly']);
				}
			}
		}
		
		if ( isset($_POST['wp_member_offset']) && $_POST['wp_member_offset'] == $offset && !empty($_POST['wp_member_periods'])) {
			$users = $params['currentuser'] == 'y'? array($user): explode('|', $_POST['wp_member_users']);
			$users = array_map('trim', $users);
			$users = array_filter($users, array( $userlib, 'user_exists' ));
			$users = array_filter($users);
			$smarty->assign('wp_member_users', count($users));

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
			if ($prefs['payment_system'] == 'paypal' && $oneuser) {
				$rounded = round($periods);
				if (($ppinterval== 'Y' && $rounded <= 5) || ($ppinterval== 'M' && $rounded <= 24)
					|| ($ppinterval== 'D' && $rounded <= 90))
				{
					$smarty->assign('wp_member_subscribeok', 'y');
					if (isset($ppunits)) {
						$smarty->assign('wp_member_periodset', $ppunits);
					} else {
						$smarty->assign('wp_member_periodset', round($periods));
					}
					if (isset($ppinterval)) {
						$smarty->assign('wp_member_interval', $ppinterval);
					}
				//when parameters don't fit within paypal limits
				} else {
					$smarty->assign('wp_member_subscribeok', 'n');
				}
			}

			if ( count($users) == 1 ) {
				$mem = isset($extendinfo['new']) && $extendinfo['new'] === false ? 'Extend membership' : 'Membership';
				$term = !empty($info['descString']) ? ' ' . $info['descString'] : '';
				$desc = tr('%0 to %1 %2 for %3', $mem, $params['group'], $term, reset($users));
			} else {
				$perplural = $periods > 1 ? tra('periods') : tra('period');
				$desc = tr('Membership to %0 for %1 users for %2 %3', $params['group'], count($users), $periods,
					$perplural);
			}

			//calculate cost
			$cost = 0;
			if (!empty($info['expireAfter'])) {
				$cost = round(count($users) * ($periods - $freeperiods) * $params['price'], 2);
			}
			if ($info['anniversary'] > '') {
				foreach ($users as $u) {
					$extendinfo = $userlib->get_extend_until_info($u, $params['group'], $periods);
					$extendinfo['freeperiods'] = $freeperiods;
					if (!empty($extendinfo['ratio_prorated_first_period'])
						&& $extendinfo['ratio_prorated_first_period'] > 0
						&& $extendinfo['ratio_prorated_first_period'] < 1)
					{
						$smarty->assign('wp_member_prorated', round($extendinfo['ratio_prorated_first_period']
							* $params['price'], 2));
					}
					if ($extendinfo['freeperiods'] > 0 && $extendinfo['ratio_prorated_first_period'] < 1
						&& $extendinfo['ratio_prorated_first_period'] > 0)
					{
						$extendinfo['ratio'] = $extendinfo['ratio'] - $extendinfo['ratio_prorated_first_period'];
						$extendinfo['freeperiods']--;
					}
					$cost += ($extendinfo['ratio'] - $extendinfo['freeperiods']) * $params['price'];
				}
				$cost = round($cost, 2);
			}

			$id = $paymentlib->request_payment($desc, $cost, $prefs['payment_default_delay']);
			$paymentlib->register_behavior($id, 'complete', 'extend_membership', array( $users, $params['group'], $periods, $info['id'] ));

			foreach ($users as $u) {
				$attributelib->set_attribute('user', $u, 'tiki.memberextend.' . $info['id'], $id);
			}
			$paymentlib->register_behavior($id, 'cancel', 'cancel_membership_extension', array( $users, $info['id'] ));
				$smarty->assign('wp_member_title', $params['howtitle']);
				$smarty->assign('wp_member_titleonly', $params['howtitleonly']);
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment(array( 'id' => $id ), $smarty) . '~/np~^';
		} else if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && isset($_POST['invoice'])) {
			require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment(array( 'id' => $_POST['invoice'] ), $smarty) . '~/np~^';
		}

		return '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_memberpayment.tpl') . '~/np~';
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