<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


/**
 * Performs the second half of a trade - "offer mode" only and also cclite only
 * The payment system does the first half ($user to manager account), this does manager to destination
 * 
 * Work in progress
 * 
 * @param string $main_user
 * @param string $other_user
 * @param float $price
 * @param string $currency
 * @param string $wanted = 'n'
 */
function payment_behavior_perform_trade( $main_user, $other_user, $price, $currency = '', $wanted = 'n' ) {
	global $userlib, $paymentlib, $prefs, $cclitelib, $smarty;
	require_once 'lib/payment/cclitelib.php';
	$smarty->assign('ccresult_ok', false);
	
	
	if (!$userlib->user_exists( $main_user)) {
		$smarty->assign('ccresult2', "Perform Trade: Main user $main_user not found");
	}
	if (!$userlib->user_exists( $other_user)) {
		$smarty->assign('ccresult2', "Perform Trade: Other user $other_user not found");
	}
	
	if (!$price || (int) $price === 0) {
		$smarty->assign('ccresult2', "Perform Trade: price not set");
	}
	
	$result = $cclitelib->pay_user( $price, $currency, '', $other_user );
	
	if (!empty($result)) {
		$smarty->assign('ccresult2', $result);
		$smarty->assign('ccresult_ok', (strpos($result, 'Transaction Accepted') !== false));
	} else {
		$smarty->assign('ccresult2', tr('Payment sent but verification not currently available. (Work in progress)'));
	}
	
}

