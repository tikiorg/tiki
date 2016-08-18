<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PaymentLib extends TikiDb_Bridge
{
	private $gateways = array();

	public $fieldmap = [
		'paymentRequestId' => [
			'table' => 'tpr',
			],
		'description' => [
			'table' => 'tpr',
		],
		'detail' => [
			'table' => 'tpr',
		],
		'amount' => [
			'table' => 'tpr',
		],
		'request_date' => [
			'table' => 'tpr',
		],
		'payment_date' => [
			'table' => 'tp',
		],
		'type' => [
			'table' => 'tp',
		],
		'login' => [
			'table' => 'uu',
		],
		'payer' => [
			'table' => 'uup',
			'field' => 'login',
		],
	];

	private function setTable($field)
	{
		return isset($this->fieldmap[$field]['table']) ? $this->fieldmap[$field]['table'] : '';
	}

	private function setField($field)
	{
		return isset($this->fieldmap[$field]['field']) ? $this->fieldmap[$field]['field'] : $field;
	}

	private function fieldTableArray()
	{
		$ret = [];
		foreach ($this->fieldmap as $field => $info) {
			$table = $this->setTable($field);
			$rfield = $this->setField($field);
			$ret[] = $table . '.' . $rfield;
		}
		return $ret;
	}

	function request_payment( $description, $amount, $paymentWithin, $detail = null, $currency = null )
	{
		global $prefs, $user;
		$userlib = TikiLib::lib('user');

		$description = substr($description, 0, 100);
		if (empty($currency)) {
			$currency = $prefs['payment_currency'];
		} else {
			$currency = substr($currency, 0, 3);
		}

		$query = 'INSERT INTO `tiki_payment_requests`' .
						' ( `amount`, `amount_paid`, `currency`, `request_date`, `due_date`, `description`, `detail`, `userId` )' .
						' VALUES( ?, 0, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ?, ?, ? )';

		$bindvars = array( $amount, $currency, (int) $paymentWithin, $description, $detail, $userlib->get_user_id($user) );

		$this->query($query, $bindvars);

		return $this->lastInsertId();
	}

	private function get_payments( $conditions, $offset, $max, $what='' )
	{
		$mid = '`tiki_payment_requests` tpr LEFT JOIN `users_users` uu ON (uu.`userId` = tpr.`userId`)';
		$count = 'SELECT COUNT(*) FROM ' . $mid . ' WHERE ' . $conditions;
		$data = 'SELECT tpr.*, uu.`login` as `user` '.$what.' FROM ' . $mid . ' WHERE ' . $conditions;

		$all = $this->fetchAll($data, array(), $max, $offset);

		return array(
			'cant' => $this->getOne($count),
			'data' => Perms::filter(
				array( 'type' => 'payment' ),
				'object',
				$all,
				array( 'object' => 'paymentRequestId' ),
				'payment_view'
			),
		);
	}

	function get_outstanding( $offset, $max, $ofUser = '', $filter = [], $sort = null )
	{
		$conditions = '`amount_paid` < `amount` AND NOW() <= `due_date` AND `cancel_date` IS NULL AND (`authorized_until` IS NULL OR `authorized_until` <= NOW())';
		if ($ofUser) {
			$conditions .= " AND uu.`login` = " . $this->qstr($ofUser);
		}
		$conditions .= $this->addFilterSort($filter, $sort);
		return $this->get_payments($conditions, $offset, $max);
	}

	function get_past( $offset, $max, $ofUser = '', $filter = [], $sort = null )
	{
		global $prefs;
		$parserlib = TikiLib::lib('parser');

		$conditions = 'tpr.`amount` <= tpr.`amount_paid` AND tpr.`cancel_date` IS NULL';
		if ($ofUser) {
			$conditions .= " AND uu.`login` = " . $this->qstr($ofUser);
		}
		$conditions .= $this->addFilterSort($filter, $sort);

		$count = 'SELECT COUNT(*)' .
			' FROM `tiki_payment_requests` tpr' .
			' LEFT JOIN `users_users` uu ON (uu.`userId` = tpr.`userId`)' .
			' LEFT JOIN `tiki_payment_received` tp ON (tp.`paymentRequestId`=tpr.`paymentRequestId` AND tp.`status` = "paid")' .
			' LEFT JOIN `users_users` uup ON (uup.`userId` = tp.`userId`) WHERE ' . $conditions;

		$data = 'SELECT tpr.*, uu.`login` as `user`, tp.`type`, tp.`payment_date`,' .
			' tp.`details` as `payment_detail`, tpr.`detail` as `request_detail`, uup.`login` as `payer`' .
			' FROM `tiki_payment_requests` tpr' .
			' LEFT JOIN `users_users` uu ON (uu.`userId` = tpr.`userId`)' .
			' LEFT JOIN `tiki_payment_received` tp ON (tp.`paymentRequestId`=tpr.`paymentRequestId` AND tp.`status` = "paid")' .
			' LEFT JOIN `users_users` uup ON (uup.`userId` = tp.`userId`) WHERE ' . $conditions;

		$all = $this->fetchAll($data, array(), $max, $offset);

		foreach($all as & $payment) {

			if (empty($payment['payer'])) {	// anonymous
				$details = json_decode($payment['payment_detail'], true);
				if ($details && !empty($details['payer_email'])) {
					$payment['payer_email'] = $details['payer_email'];
				}
			}

			if (!empty($payment['request_detail']) && $prefs['feature_jquery_tablesorter']) {
				$payment['request_detail'] = strip_tags(str_replace(['</td>','</td></tr>'], [' </td>','<br></td></tr>'], $parserlib->parse_data($payment['request_detail'])), '<a><br>');
			}
		}

		return array(
			'cant' => $this->getOne($count),
			'data' => Perms::filter(
				array( 'type' => 'payment' ),
				'object',
				$all,
				array( 'object' => 'paymentRequestId' ),
				'payment_view'
			),
		);
	}

	function get_overdue( $offset, $max, $ofUser = '', $filter = [], $sort = null )
	{
		$conditions = '`amount_paid` < `amount` AND NOW() > `due_date` AND `cancel_date` IS NULL AND (`authorized_until` IS NULL OR `authorized_until` <= NOW())';
		if ($ofUser) {
			$conditions .= " AND uu.`login` = " . $this->qstr($ofUser);
		}
		$conditions .= $this->addFilterSort($filter, $sort);
		return $this->get_payments($conditions, $offset, $max);
	}

	function get_authorized( $offset, $max, $ofUser = '', $filter = [], $sort = null )
	{
		$conditions = '`amount_paid` < `amount` AND `cancel_date` IS NULL AND `authorized_until` IS NOT NULL AND `authorized_until` >= NOW()';
		if ($ofUser) {
			$conditions .= " AND uu.`login` = " . $this->qstr($ofUser);
		}
		$conditions .= $this->addFilterSort($filter, $sort);
		return $this->get_payments($conditions, $offset, $max);
	}

	function get_canceled( $offset, $max, $ofUser = '', $filter = [], $sort = null )
	{
		$conditions = '`cancel_date` IS NOT NULL';
		if ($ofUser) {
			$conditions .= " AND uu.`login` = " . $this->qstr($ofUser);
		}
		$conditions .= $this->addFilterSort($filter, $sort);
		return $this->get_payments($conditions, $offset, $max);
	}

	function uncancel_payment( $id )
	{
		$this->query('UPDATE `tiki_payment_requests` SET `cancel_date` = NULL WHERE `paymentRequestId` = ?', array( $id ));
	}

	function cancel_payment($id)
	{
		if ( $info = $this->get_payment($id) ) {
			if ( $info['state'] != 'canceled' ) {
				$this->run_behaviors($info, 'cancel');
			}
		}

		$this->query('UPDATE `tiki_payment_requests` SET `cancel_date` = NOW() WHERE `paymentRequestId` = ?', array( $id ));
	}

	function get_payment( $id )
	{
		global $tikilib, $prefs;
		$info = $this->fetchAll(
			'SELECT tpr.*, uu.`login` as `user` FROM `tiki_payment_requests` tpr' .
			' LEFT JOIN `users_users` uu ON (uu.`userId` = tpr.`userId`)' .
			' WHERE `paymentRequestId` = ?',
			array($id)
		);
		$info = reset($info);

		if ( $info ) {
			$info['state'] = $this->find_state($info);
			$info['amount_original'] = number_format($info['amount'], 2, '.', ',');
			$info['amount_remaining_raw'] = $info['amount'] - $info['amount_paid'];
			$info['amount_remaining'] = number_format($info['amount_remaining_raw'], 2, '.', ',');
			$info['url'] = $tikilib->tikiUrl(
				'tiki-payment.php',
				array('invoice' => $info['paymentRequestId'],)
			);

			$info['returnurl'] = $tikilib->tikiUrl(
				'tiki-payment.php',
				array('invoice' => $info['paymentRequestId'],)
			);

			// Add token if feature is activated (need prefs
			global $user;
			if ($prefs['auth_token_access'] == 'y' &&
					(!$user || isset($_SESSION['forceanon']) &&
					$_SESSION['forceanon'] == 'y' &&
					!Perms::get('payment', $info['paymentRequestId'])->manual_payment)
			) {
				require_once('lib/wiki-plugins/wikiplugin_getaccesstoken.php');
				$info['returnurl'] = $tikilib->tikiUrl(
					'tiki-payment.php',
					array(
						'invoice' => $info['paymentRequestId'],
						'TOKEN' => wikiplugin_getaccesstoken(
							'',
							array(
								'entry' => 'tiki-payment.php',
								'keys' => array('invoice'),
								'values' => array($info['paymentRequestId'])
							)
						),
					)
				);
			}

			$info['paypal_ipn'] = $tikilib->tikiUrl(
				'tiki-payment.php',
				array('ipn' => 1,'invoice' => $info['paymentRequestId'],)
			);

			$info['payments'] = array();

			$payments = $this->fetchAll(
				'SELECT * FROM `tiki_payment_received` WHERE `paymentRequestId` = ? ORDER BY `payment_date` DESC',
				array($id)
			);

			foreach ( $payments as $payment ) {
				$payment['details'] = json_decode($payment['details'], true);
				$payment['amount_paid'] = number_format($payment['amount'], 2, '.', ',');
				$info['payments'][] = $payment;
			}

			$info['actions'] = $this->extract_actions($info['actions']);

			return $info;
		}
	}

	private function find_state( $info )
	{
		if ( ! empty($info['cancel_date']) ) {
			return 'canceled';
		}

		if ( $info['amount_paid'] >= $info['amount'] ) {
			return 'past';
		}

		$current = date('Y-m-d H:i:s');

		if ( $info['authorized_until'] && $info['authorized_until'] > $current ) {
			return 'authorized';
		}

		if ( $info['due_date'] < $current ) {
			return 'overdue';
		}

		return 'outstanding';
	}

	private function extract_actions( $actions )
	{
		$out = array(
			'authorize' => array(),
			'complete' => array(),
			'cancel' => array(),
		);
		if ( ! empty($actions) ) {
			$out = array_merge(
				$out,
				json_decode($actions, true)
			);
		}

		return $out;
	}

	function enter_payment( $invoice, $amount, $type, array $data )
	{
		$tx = TikiDb::get()->begin();

		global $user;
		$userlib = TikiLib::lib('user');
		if ( $info = $this->get_payment($invoice) ) {
			if ( $info['state'] != 'past' && $info['amount_remaining_raw'] - $amount <= 0 ) {
				$results = $this->run_behaviors($info, 'complete');
				if ($info['state'] == 'canceled') {
					// in the case of canceled payments being paid (e.g. user was delayed at Paypal when cancellation happened)
					$this->uncancel_payment($invoice);
				}
			}
			if (!empty($results)) {
				$data = array_merge($results, $data);
			}
			$data = json_encode($data);
			$this->query(
				'INSERT INTO `tiki_payment_received` ( `paymentRequestId`, `payment_date`, `amount`, `type`, `details`, `userId` )' .
				' VALUES( ?, NOW(), ?, ?, ?, ? )',
				array(
					$invoice,
					$amount,
					$type,
					$data,
					empty($user)? $info['userId']: $userlib->get_user_id($user)
				)
			);
			$this->query(
				'UPDATE `tiki_payment_requests` SET `amount_paid` = `amount_paid` + ? WHERE `paymentRequestId` = ?',
				array( $amount, $invoice )
			);
		}

		$tx->commit();
	}

	function enter_authorization( $invoice, $type, $validForDays, array $data )
	{
		global $user;
		$userlib = TikiLib::lib('user');
		if ( $info = $this->get_payment($invoice) ) {
			if ( $info['state'] != 'past' ) {
				$results = $this->run_behaviors($info, 'authorize');
				if ($info['state'] == 'canceled') {
					// in the case of canceled payments being paid (e.g. user was delayed at Paypal when cancellation happened)
					$this->uncancel_payment($invoice);
				}
			}
			if (!empty($results)) {
				$data = array_merge($results, $data);
			}
			$data = json_encode($data);
			$this->query(
				'INSERT INTO `tiki_payment_received` ( `paymentRequestId`, `payment_date`, `amount`, `type`, `details`, `userId`, `status` )' .
				' VALUES( ?, NOW(), ?, ?, ?, ?, "auth_pending" )',
				array(
					$invoice,
					0,
					$type,
					$data,
					empty($user)? $info['userId']: $userlib->get_user_id($user)
				)
			);
			$this->query(
				'UPDATE `tiki_payment_requests` SET `authorized_until` = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE `paymentRequestId` = ?',
				array( (int) $validForDays, $invoice )
			);
		}
	}

	function capture_payment($paymentId)
	{
		if ($info = $this->get_payment($paymentId)) {
			foreach ($info['payments'] as $received) {
				if ($received['status'] != 'auth_pending') {
					continue;
				}

				if ($received['amount']) {
					// When electing to capture a specific amount, assume that amount is the total to be paid.
					$table = $this->table('tiki_payment_requests');
					$table->update(['amount' => (float) $received['amount']], ['paymentRequestId' => $paymentId]);
				}

				if ($gateway = $this->gateway($received['type'])) {
					if (is_callable(array($gateway, 'capture_payment'))) {
						// Result is about request reception success, not actual capture success
						$result = $gateway->capture_payment($info, $received);
						
						if ($result) {
							$this->query('UPDATE `tiki_payment_received` SET `status` = "auth_captured" WHERE `paymentReceivedId` = ?', array($received['paymentReceivedId']));
						}
					}
				}
			}
		}
	}

	function register_behavior( $invoice, $event, $behavior, array $arguments )
	{
		if ( ! in_array($event, array( 'complete', 'cancel', 'authorize' )) ) {
			return false;
		}

		if ( ! $callback = $this->get_behavior($behavior) ) {
			return false;
		}

		if ( $info = $this->get_payment($invoice) ) {
			$actions = $info['actions'];

			$actions[$event][] = array( 'behavior' => $behavior, 'arguments' => $arguments );
			$this->query(
				'UPDATE `tiki_payment_requests` SET `actions` = ? WHERE `paymentRequestId` = ?',
				array(json_encode($actions), $invoice)
			);
		} else {
			return false;
		}
	}

	private function run_behaviors( $info, $event )
	{
		$behaviors = $info['actions'][$event];
		$results = array();

		foreach ( $behaviors as $b ) {
			if ( $callback = $this->get_behavior($b['behavior']) ) {
				$results[str_replace('payment_behavior_', '', $callback)] = call_user_func_array($callback, $b['arguments']);
			}
		}
		return $results;
	}

	private function get_behavior( $name )
	{
		$file = dirname(__FILE__) . "/behavior/$name.php";
		$function = 'payment_behavior_' . $name;
		if ( is_readable($file) ) {
			require_once $file;
			if ( is_callable($function) ) {
				return $function;
			}
		}
	}

	function gateway($name)
	{
		if (isset($this->gateways[$name])) {
			return $this->gateways[$name];
		}

		switch ($name) {
		case 'israelpost':
			require_once 'lib/payment/israelpostlib.php';
			return $this->gateways[$name] = new IsraelPostLib($this);
		}
	}

	private function addFilterSort(array $filter, $sort)
	{
		$ret = '';
		if (!empty($filter)) {
			foreach ($filter as $field => $value) {
				if (isset($this->fieldmap[$field])) {
					$table = $this->setTable($field);
					$col = $this->setField($field);
					$ret .= " AND " . $table . '.`' . $col . '`';
					if ($field == 'description' || $field == 'detail') {
						$ret .= " LIKE '%" . $value . "%'";
					} elseif (in_array($field, ['payment_date', 'request_date'])) {
						$ret .= ' ' . $value;
					} else {
						$ret .= " LIKE '" . $value . "%'";
					}
				}
			}
		}
		if (!empty($sort)) {
			if (!is_array($sort)) {
				$sort = explode(',', $sort);
			}
			foreach($sort as $s) {
				if (strpos($s, '.') === false) {
					$dir = strrchr($s, '_');
					$sfield = substr($s, 0, strlen($s) - strlen($dir));
					$stable = $this->setTable($sfield);
					$scol = $this->setField($sfield);
					$newsort[] = $stable . '.' . $scol . $dir;
				}
			}
			if (!empty($newsort)) {
				$fields = $this->fieldTableArray();
				$newsort = implode(',', $newsort);
				$ret .= ' ORDER BY ' . $this->convertSortMode($newsort, $fields);
			}
		}
		return $ret;
	}
}

