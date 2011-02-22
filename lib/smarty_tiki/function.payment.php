<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// @param numeric $id: id of the payment
// @params url $returnurl: optional return url
function smarty_function_payment( $params, $smarty ) {
	global $tikilib, $prefs, $userlib, $user, $globalperms;
	global $paymentlib; require_once 'lib/payment/paymentlib.php';
	$invoice = (int) $params['id'];

	$objectperms = Perms::get( 'payment', $invoice );
	$info = $paymentlib->get_payment( $invoice );
	if ($user && $info['userId'] == $userlib->get_user_id($user)) {
		$theguy = true;
	} else {
		$theguy = false;
	}
	$smarty->assign('ccresult_ok', false);
	
	// Unpaid payments can be seen by anyone as long as they know the number
	// Just like your bank account, anyone can drop money in it.
	if( $info && $objectperms->payment_view && (($info['state'] == 'outstanding' || $info['state'] == 'overdue') && $prefs['payment_user_only_his_own'] != 'y' || $info['state'] == 'past' && $prefs['payment_user_only_his_own_past'] != 'y' || $theguy )) { 
		if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && $_POST['cclite_payment_amount'] == $info['amount_remaining']) {
			global $access, $cclitelib, $cartlib;
			require_once 'lib/payment/cclitelib.php';
			require_once 'lib/payment/cartlib.php';
			
			//$access->check_authenticity( tr('Transfer currency? %0 %1?', $info['amount'], $info['currency'] ));
			
			// check currency matches
			if (empty($params['registry'])) { $params['registry'] = $cclitelib->get_registry(); }
			if (empty($info['currency'])) {
				$info['currency'] = $cclitelib->get_currency($params['registry']);
			} else {
				if ($info['currency'] != substr($cclitelib->get_currency($params['registry']), 0, 3)) {
					return tr('Currency in payment (%0) does not match the currency for that registry (%1).', $info['currency'], $cclitelib->get_currency($params['registry']) );
				}
			}
			
			// no notification callback in cclite yet, so have to assume true for now (pending checking in perform_trade)
			$result = $cclitelib->pay_invoice($invoice, $info['amount'], $info['currency'], $params['registry']);
			if ($result) {
				// ccresults are set in smarty by the perform_trade behaviour
				$smarty->assign('ccresult', $result);
				$smarty->assign('ccresult_ok', $result);
			} else {
				$smarty->assign('ccresult', tr('Payment sent but verification not currently available. (Work in progress)'));
			}
		} else if ( $prefs['payment_system'] == 'tikicredits') {
			require_once 'lib/payment/creditspaylib.php';
			$userpaycredits = new UserPayCredits;
			$userpaycredits->setPrice($info['amount_remaining']);
			$smarty->assign('userpaycredits',$userpaycredits->credits);
		}

		
		$info['fullview'] = $objectperms->payment_view || $theguy;
		if (!empty($params['returnurl']) && empty($result)) {
			$info['url'] = preg_match('|^https?://|', $params['returnurl'])? $params['returnurl']: $tikilib->tikiUrl($params['returnurl']);
			$info['url'] .= (strstr($params['returnurl'], '.php?') || !strstr($params['returnurl'],'.php')? '&':'?')."invoice=$invoice";
		}
		$smarty->assign( 'payment_info', $info );
		$smarty->assign( 'payment_detail', $tikilib->parse_data( htmlspecialchars($info['detail']) ) );
		return $smarty->fetch( 'tiki-payment-single.tpl' );
	} else {
		return tra('This invoice does not exist or is in limited access.');
	}
}
