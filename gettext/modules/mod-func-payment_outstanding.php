<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_payment_outstanding_info() {
	return array(
		'name' => tra('Payments Outstanding'),
		'description' => tra('Displays the payments outstanding for the current user.'),
		'prefs' => array( 'payment_feature' ),
	);
}

function module_payment_outstanding( $mod_reference, $module_params ) {
	global $smarty, $user, $paymentlib, $prefs;

	require_once 'lib/payment/paymentlib.php';
	if ($user) {
		$data = $paymentlib->get_outstanding(0, $mod_reference['rows'], $user); 
		$smarty->assign( 'outstanding', $data );
	}
}

