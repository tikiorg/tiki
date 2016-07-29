<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Data sent by the IPN must be left unharmed
if ( isset( $_GET['ipn'] ) ) {
	$ipn_data = $_POST;
}
$inputConfiguration = array(
		array(
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
				'offset_authorized' => 'digits',
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
				'returnurl' => 'url',
				'tsAjax' => 'word',
				'list_type' => 'word',
				'sort_mode' => 'text',
				'numrows' => 'digits',
				'filter_paymentRequestId' => 'digits',
				'filter_description' => 'text',
				'filter_detail' => 'text',
				'filter_amount' => 'text',
				//need to allow <= and >= for these - will filter later
				'filter_request_date' => 'none',
				'filter_payment_date' => 'none',
				'filter_type' => 'text',
				'filter_login' => 'text',
				'filter_payer' => 'text',
			),
			'staticKeyFiltersForArrays' => array('cart' => 'digits',),	// params for cart module
			'catchAllUnset' => null,
			)
);

require_once 'tiki-setup.php';
$categlib = TikiLib::lib('categ');
$paymentlib = TikiLib::lib('payment');
$access->check_feature('payment_feature');

$auto_query_args = array(
		'offset_outstanding',
		'offset_overdue',
		'offset_past',
		'offset_canceled',
);

if ( isset($_POST['tiki_credit_pay'])
			&& isset($_POST['tiki_credit_amount'])
			&& isset($_POST['tiki_credit_type'])
			&& isset($_POST['invoice'])
) {
	require_once 'lib/payment/creditspaylib.php';
	$userpaycredits = new UserPayCredits;
	$userpaycredits->payAmount($_POST['tiki_credit_type'], $_POST['tiki_credit_amount'], $_POST['invoice']);
}

if ( isset($ipn_data) ) {
	$access->check_feature('payment_paypal_ipn');
	require_once 'lib/payment/paypallib.php';

	$invoice = $paypallib->get_invoice($ipn_data);
	if (!is_numeric($invoice) || $invoice < 1) {
		echo 'Payment response was not correctly formatted';	// goes back to PayPal server - for debugging mainly
		exit;
	}
	$info = $paymentlib->get_payment($invoice);

	// Important to check with paypal first
	if (isset($info) && $paypallib->is_valid($ipn_data, $info)) {
		$amount = $paypallib->get_amount($ipn_data);
		$paymentlib->enter_payment($invoice, $amount, 'paypal', $ipn_data);
	} else {
		echo 'Payment '.$invoice.' was not verified';	// goes back to PayPal server
		exit;
	}

	exit;
}

if ($prefs['payment_system'] == 'israelpost' && isset($_GET['invoice']) && $jitGet->OKauthentication->word()) {
	$gateway = $paymentlib->gateway('israelpost');
	// Return URL - check payment right away through APIs
	$id = $_GET['invoice'];
	$verified = $gateway->check_payment($id, $jitGet, $jitPost);

	if ($verified) {
		$access->redirect('tiki-payment.php?invoice=' . $id, tra('Payment has been confirmed.'));
	} else {
		$access->redirect('tiki-payment.php?invoice=' . $id, tra('Payment confirmation has not been received yet.'));
	}
}

if ( isset( $_POST['manual_amount'], $_POST['invoice'] ) && preg_match('/^\d+(\.\d{2})?$/', $_POST['manual_amount']) ) {
	$objectperms = Perms::get('payment', $_REQUEST['invoice']);

	if ( $objectperms->payment_manual ) {
		$paymentlib->enter_payment(
			$_POST['invoice'],
			$_POST['manual_amount'],
			'user',
			array(
				'user' => $user,
				'note' => $_POST['note'],
			)
		);
		if (isset($_POST['returnurl'])) {
			header('Location: ' . $_POST['returnurl']);
			exit;
		}

		$access->redirect('tiki-payment.php?invoice=' . $_POST['invoice'], tra('Manual payment entered.'));
	} else {
		$access->redirect('tiki-payment.php?invoice=' . $_POST['invoice'], tra('You do not have permission to enter payment.'));
	}
}

if ( isset($_POST['request']) && $globalperms->request_payment ) {
	// Create new payment request

	if ( ! empty($_POST['description']) && preg_match('/^\d+(\.\d{2})?$/', $_POST['amount']) && $_POST['payable'] > 0 ) {
		$id = $paymentlib->request_payment($_POST['description'], $_POST['amount'], (int) $_POST['payable'], $_POST['detail']);

		if ( $prefs['feature_categories'] == 'y' ) {
			$cat_objid = $id;
			$cat_type = 'payment';
			$cat_desc = $_POST['description'];
			$cat_name = $_POST['description'];
			$cat_href = 'tiki-payment.php?invoice=' . $id;
			require 'categorize.php';
		}

		$access->redirect('tiki-payment.php?invoice=' . $id, tra('New payment requested.'));
	}
}

if ( isset($_REQUEST['cancel']) ) {
	$objectperms = Perms::get('payment', $_REQUEST['cancel']);
	$info = $paymentlib->get_payment($_REQUEST['cancel']);

	if ( $objectperms->payment_admin || $info['user'] == $user ) {
		$access->check_authenticity(tr('Cancel payment %0?', $_REQUEST['cancel']));
		$paymentlib->cancel_payment($_REQUEST['cancel']);
		$access->redirect('tiki-payment.php?invoice=' . $_REQUEST['cancel'], tra('Payment canceled.'));
	}
}

// Obtain information
/**
 * @param $type
 */
function fetch_payment_list($type)
{
	global $globalperms, $user, $prefs;
	$smarty = TikiLib::lib('smarty');
	$paymentlib = TikiLib::lib('payment');
	$offsetKey = 'offset_' . $type;
	$method = 'get_' . $type;
	$offset = isset($_REQUEST[$offsetKey]) ? intval($_REQUEST[$offsetKey]) : 0;
	$max = !empty($_REQUEST['numrows']) ? $_REQUEST['numrows'] : intval($prefs['maxRecords']);

	if (!empty($_REQUEST)) {
		$fields = array_keys($paymentlib->fieldmap);
		foreach ($fields as $field) {
			if (array_key_exists('filter_' . $field, $_REQUEST)) {
				$filter[$field] = $_REQUEST['filter_' . $field];
			}
		}
	}
	$filter = !empty($filter) ? $filter : [];
	$dfields = ['request_date', 'payment_date'];
	foreach ($dfields as $dfield) {
		if (!empty($filter[$dfield])) {
			$datefilter = explode(' - ', $filter[$dfield]);
			if (count($datefilter) === 2) {
				$tsfrom =  intval(substr($datefilter[0], 0, 10));
				$fromobj = new DateTime("@$tsfrom");
				$tsto =  intval(substr($datefilter[1], 0, 10));
				$toobj = new DateTime("@$tsto");
				$filter[$dfield] = '>= \'' . $fromobj->format('Y-m-d H:i:s') . '\' AND '
					. $paymentlib->fieldmap[$dfield]['table'] . '.`' . $dfield . '` <= \''
					. $toobj->format('Y-m-d H:i:s') . '\'';
			} else {
				$ts = intval(substr($filter[$dfield], 2, 10));
				$dateobj = new DateTime("@$ts");
				$op = substr($filter[$dfield], 0, 2);
				$op = in_array($op, ['<=', '>=']) ?  $op : '';
				if ($op) {
					$filter[$dfield] = substr($filter[$dfield], 0, 2) . ' \'' . $dateobj->format('Y-m-d H:i:s') . '\'';
				} else {
					unset($filter[$dfield]);
				}
			}
		}
	}

	$sort = !empty($_REQUEST['sort_mode']) ? $_REQUEST['sort_mode'] : null;

	$forUser = '';
	if (!$globalperms->payment_admin
				&& (
					($type == 'outstanding' || $type == 'overdue')
					&& $prefs['payment_user_only_his_own'] == 'y'
					|| $type != 'outstanding'
					&& $type != 'overdue'
					&& $prefs['payment_user_only_his_own_past'] == 'y'
				)
	) {
		$forUser = $user;
	}
	$data = $paymentlib->$method($offset, $max, $forUser, $filter, $sort);
	$data['offset'] = $offset;
	$data['offset_arg'] = "offset_$type";
	$data['max'] = $max;
	$smarty->assign($type, $data);

	//add tablesorter sorting and filtering
	$ts['enabled'] = Table_Check::isEnabled(true);
	$ts['ajax'] = Table_Check::isAjaxCall();
	static $iid = 0;
	++$iid;
	$ts['tableid'] = 'pmt_' . $type . $iid;
	$smarty->assign('ts', $ts);

	if ($ts['enabled'] && !$ts['ajax']) {
		$tableclass = $type == 'past' ? 'TikiPaymentPast' : 'TikiPayment';
		Table_Factory::build(
			$tableclass,
			array(
				'id' => 'pmt_' . $type,
				'total' => $data['cant'],
				'ajax' => array(
					'requiredparams' => array(
						'list_type' => $type,
					),
				)
			)
		);
	}
}

if ( $prefs['feature_categories'] == 'y' && $globalperms->payment_request ) {
	$cat_type = 'payment';
	$cat_objid = '';
	$cat_object_exists = false;
	$smarty->assign('section', 'payment');
	require 'categorize_list.php';
}

if ( isset($_REQUEST['invoice']) ) {
	$smarty->assign('invoice', $_REQUEST['invoice']);
}

if (Table_Check::isEnabled(true) && Table_Check::isAjaxCall()) {
	$types = ['outstanding', 'overdue', 'past', 'canceled', 'authorized'];
	if (!empty($_REQUEST['list_type']) && in_array($_REQUEST['list_type'], $types)) {
		fetch_payment_list($_REQUEST['list_type']);
	}
	$smarty->display('tiki-payment.tpl');
} else {
	fetch_payment_list('outstanding');
	fetch_payment_list('overdue');
	fetch_payment_list('past');
	fetch_payment_list('canceled');
	fetch_payment_list('authorized');
	$smarty->assign('mid', 'tiki-payment.tpl');
	$smarty->display('tiki.tpl');
}


