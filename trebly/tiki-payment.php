<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Data sent by the IPN must be left unharmed
if( isset( $_GET['ipn'] ) ) {
	$ipn_data = $_POST;
}

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'amount' => 'text',
		'manual_amount' => 'text',
		'description' => 'text',
		'request' => 'alpha',
		'payable' => 'digits',
		'offset_outstanding' => 'digits',
		'offset_overdue' => 'digits',
		'offset_past' => 'digits',
		'offset_canceled' => 'digits',
		'invoice' => 'digits',
		'cancel' => 'digits',
		'note' => 'striptags',
		'detail' => 'wikicontent',
		'cclite_payment_amount' => 'text',	// params for cart module
		'tiki_credit_amount' => 'text',
		'tiki_credit_pay' => 'text',
		'tiki_credit_type' => 'text',
		'checkout' => 'text',
		'update' => 'word',
		'daconfirm' => 'word',				// ticketlib
		'ticket' => 'word',
	),
	'staticKeyFiltersForArrays' => array(
		'cart' => 'digits',	// params for cart module
	),
	'catchAllUnset' => null,
) );

require_once 'tiki-setup.php';
require_once 'lib/categories/categlib.php';
require_once 'lib/payment/paymentlib.php';
$access->check_feature( 'payment_feature' );
$auto_query_args = array(
	'offset_outstanding',
	'offset_overdue',
	'offset_past',
	'offset_canceled',
);

if ( isset($_POST['tiki_credit_pay']) && isset($_POST['tiki_credit_amount']) && isset($_POST['tiki_credit_type']) && isset($_POST['invoice'])) {
	require_once 'lib/payment/creditspaylib.php';
	$userpaycredits = new UserPayCredits;
	$userpaycredits->payAmount($_POST['tiki_credit_type'], $_POST['tiki_credit_amount'], $_POST['invoice']);
	
}
			
if( isset( $ipn_data ) ) {
	$access->check_feature( 'payment_paypal_ipn' );
	require_once 'lib/payment/paypallib.php';

	$invoice = $paypallib->get_invoice( $ipn_data );
	if (!is_numeric($invoice) || $invoice < 1) {
		echo 'Payment response was not correctly formatted';	// goes back to PayPal server - for debugging mainly
		exit;
	}
	$info = $paymentlib->get_payment( $invoice );

	// Important to check with paypal first
	if( $paypallib->is_valid( $ipn_data, $info ) && $info ) {
		$amount = $paypallib->get_amount( $ipn_data );
		$paymentlib->enter_payment( $invoice, $amount, 'paypal', $ipn_data );
	} else {
		echo 'Payment '.$invoice.' was not verified';	// goes back to PayPal server
		exit;
	}

	exit;
}

if( isset( $_POST['manual_amount'], $_POST['invoice'] ) && preg_match( '/^\d+(\.\d{2})?$/', $_POST['manual_amount'] ) ) {
	$objectperms = Perms::get( 'payment', $_REQUEST['invoice'] );

	if( $objectperms->payment_manual ) {
		$paymentlib->enter_payment( $_POST['invoice'], $_POST['manual_amount'], 'user', array(
			'user' => $user,
			'note' => $_POST['note'],
		) );

		$access->redirect( 'tiki-payment.php?invoice=' . $_POST['invoice'], tra('Manual payment entered.') );
	} else {
		$access->redirect( 'tiki-payment.php?invoice=' . $_POST['invoice'], tra('You do not have permission to enter payment.') );
	}
}

if( isset( $_POST['request'] ) && $globalperms->request_payment ) {
	// Create new payment request

	if( ! empty( $_POST['description'] ) && preg_match( '/^\d+(\.\d{2})?$/', $_POST['amount'] ) && $_POST['payable'] > 0 ) {
		$id = $paymentlib->request_payment( $_POST['description'], $_POST['amount'], (int) $_POST['payable'], $_POST['detail'] );

		if( $prefs['feature_categories'] == 'y' ) {
			$cat_objid = $id;
			$cat_type = 'payment';
			$cat_object_exists = false;
			$cat_desc = $_POST['description'];
			$cat_name = $_POST['description'];
			$cat_href = 'tiki-payment.php?invoice=' . $id;
			require 'categorize.php';
		}

		$access->redirect( 'tiki-payment.php?invoice=' . $id, tra('New payment requested.') );
	}
}

if( isset( $_REQUEST['cancel'] ) ) {
	$objectperms = Perms::get( 'payment', $_REQUEST['cancel'] );
	$info = $paymentlib->get_payment( $_REQUEST['cancel'] );

	if( $objectperms->payment_admin || $info['user'] == $user ) {
		$access->check_authenticity( tr('Cancel payment %0?', $_REQUEST['cancel'] ));
		$paymentlib->cancel_payment( $_REQUEST['cancel'] );
		$access->redirect( 'tiki-payment.php?invoice=' . $_REQUEST['cancel'], tra('Payment canceled.') );
	}
}

// Obtain information
function fetch_payment_list( $type ) {
	global $paymentlib, $globalperms, $user, $prefs, $smarty;
	$offsetKey = 'offset_' . $type;
	$method = 'get_' . $type;

	$offset = isset($_REQUEST[$offsetKey]) ? intval($_REQUEST[$offsetKey]) : 0;
	$max = intval( $prefs['maxRecords'] );

	$forUser = '';
	if (!$globalperms->payment_admin && ( ($type == 'outstanding' || $type == 'overdue') && $prefs["payment_user_only_his_own"] == 'y' || $type != 'outstanding' && $type != 'overdue' && $prefs["payment_user_only_his_own_past"] == 'y' ) ) {
		$forUser = $user;
	} 
	$data = $paymentlib->$method( $offset, $max, $forUser );
	$data['offset'] = $offset;
	$data['offset_arg'] = "offset_$type";
	$data['max'] = $max;

	$smarty->assign( $type, $data );
}

if( $prefs['feature_categories'] == 'y' && $globalperms->payment_request ) {
	$cat_type = 'payment';
	$cat_objid = '';
	$cat_object_exists = false;
	$smarty->assign('section','payment');
	require 'categorize_list.php';
}

if( isset( $_REQUEST['invoice'] ) ) {
	$smarty->assign( 'invoice', $_REQUEST['invoice'] );
}

fetch_payment_list( 'outstanding' );
fetch_payment_list( 'overdue' );
fetch_payment_list( 'past' );
fetch_payment_list( 'canceled' );

$smarty->assign( 'mid', 'tiki-payment.tpl' );
$smarty->display( 'tiki.tpl' );

