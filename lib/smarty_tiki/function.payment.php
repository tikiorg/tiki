<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_payment( $params, $smarty ) {
	global $tikilib, $user, $prefs;
	global $paymentlib; require_once 'lib/payment/paymentlib.php';
	$invoice = (int) $params['id'];

	$objectperms = Perms::get( 'payment', $invoice );
	$info = $paymentlib->get_payment( $invoice );
	
	// Unpaid payments can be seen by anyone as long as they know the number
	// Just like your bank account, anyone can drop money in it.
	if( $info && $info['state'] == 'outstanding' || $info['state'] == 'overdue' || $objectperms->payment_view ) {
		if ($prefs['payment_system'] == 'cclite' && isset($_POST['cclite_payment_amount']) && $_POST['cclite_payment_amount'] == $info['amount_remaining']) {
			global $cclitelib; require_once 'lib/payment/cclitelib.php';
			
			$cclitelib->pay_invoice($invoice, $info['amount'], $info['currency']);
			$smarty->assign('ccresult', tr('Payment sent but verification not currently available. (Work in progress)'));
		}

		
		$info['fullview'] = $objectperms->payment_view;
		//format for display based on user short display format and timezone
		include_once 'lib/tikilib.php';
		$info['frequest_date'] = $tikilib->format_sql_date($info['request_date']);
		$info['fdue_date'] = $tikilib->format_sql_date($info['due_date']);
		$smarty->assign( 'payment_info', $info );
		$smarty->assign( 'payment_detail', $tikilib->parse_data( htmlspecialchars($info['detail']) ) );
		return $smarty->fetch( 'tiki-payment-single.tpl' );
	} else {
		return tra('This invoice does not exist or is in limited access.');
	}
}
