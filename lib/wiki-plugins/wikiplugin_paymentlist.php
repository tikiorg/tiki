<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_paymentlist_info()
{
	return array(
		'name' => tra('Payment List'),
		'documentation' => 'PluginPaymentlist',
		'description' => tra('Show details of payments. The payments considered may be restrained by user or date.'),
		'prefs' => array( 'wikiplugin_payment', 'payment_feature' ),
		'iconname' => 'money',
		'introduced' => 16.2,
		'format' => 'html',
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('User'),
				'description' => tra('Payment type'),
				'since' => 16.2,
				'filter' => 'word',
				'default' => 'past',
				'options' => array(
					array('text' => tra('Past'), 'value' => 'past'),
					array('text' => tra('Outstanding'), 'value' => 'outstanding'),
					array('text' => tra('Cancelled'), 'value' => 'cancelled'),
					array('text' => tra('Overdue'), 'value' => 'overdue'),
					array('text' => tra('Authorized'), 'value' => 'authorized'),
				),
			),
			'format' => array(
				'required' => false,
				'name' => tra('Output format'),
				'description' => tra(''),
				'since' => 16.2,
				'filter' => 'word',
				'default' => 'total',
				'options' => array(
					array('text' => tra('Total'), 'value' => 'total'),
					array('text' => tra('Table'), 'value' => 'table'),
					array('text' => tra('JSON (for advanced custom usage)'), 'value' => 'json'),
				),
			),
			'user' => array(
				'required' => false,
				'name' => tra('User'),
				'description' => tra('Payments by a particular user'),
				'since' => 16.2,
				'filter' => 'username',
				'default' => '',
			),
			'payer' => array(
				'required' => false,
				'name' => tra('Payer email'),
				'description' => tra("Payments by anonymous where the payer's email is recorded"),
				'since' => 16.2,
				'filter' => 'email',
				'default' => '',
			),
			'date_start' => array(
				'required' => false,
				'name' => tra('Start Date'),
				'description' => tra('Date range start, accepts most date formats'),
				'since' => 16.2,
				'filter' => 'date',
				'default' => '',
			),
			'date_end' => array(
				'required' => false,
				'name' => tra('End Date'),
				'description' => tra('Date range end'),
				'since' => 16.2,
				'filter' => 'date',
				'default' => '',
			),
			'filter' => array(
				'required' => false,
				'name' => tra('Advanced filter'),
				'description' => tra('URL encoded string for advanced searching (e.g. description=Club+Membership&details=info@example.com)'),
				'since' => 16.2,
				'filter' => 'text',
				'default' => '',
			),
			'offset' => array(
				'required' => false,
				'name' => tra('List offset'),
				'description' => tra('For pagination'),
				'since' => 16.2,
				'filter' => 'digits',
				'default' => 0,
			),
			'max' => array(
				'required' => false,
				'name' => tra('Payments per page'),
				'since' => 16.2,
				'filter' => 'digits',
				'default' => -1,
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'since' => 16.2,
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => tra('Automatic (newest first)'), 'value' => ''),
					array('text' => tra('User A to Z'), 'value' => 'login_asc'),
					array('text' => tra('User Z to A'), 'value' => 'login_desc'),
					array('text' => tra('Amount low to high'), 'value' => 'amount_asc'),
					array('text' => tra('Amount high to low'), 'value' => 'amount_desc'),
					array('text' => tra('Payment: oldest first'), 'value' => 'payment_date_asc'),
					array('text' => tra('Payment: newest first'), 'value' => 'payment_date_desc'),
					array('text' => tra('Request: oldest first'), 'value' => 'request_date_asc'),
					array('text' => tra('Request: newest first'), 'value' => 'request_date_desc'),
				),
			),
		)
	);
}

function wikiplugin_paymentlist( $data, $params )
{
	static $instance = 0;

	$instance++;

	// process defaults
	$default = [];
	$plugininfo = wikiplugin_paymentlist_info();
	foreach ($plugininfo['params'] as $key => $p) {
		$default[$key] = $p['default'];
	}
	$params = array_merge($default, $params);

	$output = '';
	$paymentlib = TikiLib::lib('payment');
	$payments = [];
	$template = 'tiki-payment-list.tpl';

	parse_str($params['filter'], $filter);	// convert url type filter (e.g. 'description=foo' to array (description=>foo)

	// make date filter
	$date_field = $params['type'] === 'past' ? 'payment_date' : 'request_date';
	$start = strtotime($params['date_start']);
	$end = strtotime($params['date_end']);

	if ($start && $end) {
		$filter[$date_field] = "between FROM_UNIXTIME($start) and FROM_UNIXTIME($end)";
	} else if ($start) {
		$filter[$date_field] = "> FROM_UNIXTIME($start)";
	} else if ($end) {
		$filter[$date_field] = "< FROM_UNIXTIME($end)";
	}

	// payer filter
	if ($params['payer']) {
		if (isset($filter['details'])) {
			Feedback::error(tra('Note, the paymentlist "payer" parameter cannot be used when search for "details" in an advance filter.'));
		} else {
			$filter['details'] = '"payer_email":"' . $params['payer'] .'"';
		}
	}

	// list management
	$offset_arg = "offset_{$params['type']}_$instance";
	if (empty($params['offset']) && !empty($_REQUEST[$offset_arg])) {
		$params['offset'] = $_REQUEST[$offset_arg];
	}

	if ($params['type'] === 'past') {

		$template = 'tiki-payment-list-past.tpl';
		$payments = $paymentlib->get_past($params['offset'], $params['max'], $params['user'], $filter, $params['sort']);

	} else if ($params['type'] === 'outstanding') {

		$payments = $paymentlib->get_outstanding($params['offset'], $params['max'], $params['user'], $filter, $params['sort']);

	} else if ($params['type'] === 'cancelled') {

		$payments = $paymentlib->get_canceled($params['offset'], $params['max'], $params['user'], $filter, $params['sort']);

	} else if ($params['type'] === 'overdue') {

		$payments = $paymentlib->get_overdue($params['offset'], $params['max'], $params['user'], $filter, $params['sort']);

	} else if ($params['type'] === 'authorized') {

		$payments = $paymentlib->get_authorized($params['offset'], $params['max'], $params['user'], $filter, $params['sort']);

	} else {

		Feedback::error(tr('Plugin paymentlist: Unrecognised type "%0"'), $params['type']);
	}

	if ($params['format'] === 'total') {
		$totals = [];
		foreach ($payments['data'] as $payment) {
			$totals[$payment['currency']] += $payment['amount'];
		}
		if (count($totals) > 0) {	// multiple currencies?
			foreach ($totals as $currency => $amount) {
				$output .= "$amount $currency, ";
			}
			$output = trim($output, ', ');
		} else {
			$output = tra('No payments found');
		}

	} else if ($params['format'] === 'table') {

		if ($payments['data']) {

			$payments['offset'] = $params['offset'];
			$payments['offset_arg'] = $offset_arg;
			$payments['max'] = $params['max'];

			$smarty = TikiLib::lib('smarty');
			$smarty->assign('payments', $payments);
			$output = $smarty->fetch($template);
		} else {
			$output = tra('No payments found');
		}

	} else if ($params['format'] === 'json') {
		$output = json_encode($payments['data']);
	}

	return $output;
}

