<?php

class PaymentLib extends TikiDb_Bridge
{
	function request_payment( $description, $amount, $paymentWithin ) {
		global $prefs;

		$description = substr( $description, 0, 100 );

		$query = 'INSERT INTO `tiki_payment_requests` ( `amount`, `amount_paid`, `currency`, `request_date`, `due_date`, `description` ) VALUES( ?, 0, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ? )';
		$bindvars = array( $amount, $prefs['payment_currency'], (int) $paymentWithin, $description );

		$this->query( $query, $bindvars );

		return $this->getOne( 'SELECT MAX(`paymentRequestId`) FROM `tiki_payment_requests` WHERE `description` = ?', array( $description ) );
	}

	private function get_payments( $conditions, $offset, $max ) {
		$count = 'SELECT COUNT(*) FROM `tiki_payment_requests` WHERE ' . $conditions;
		$data = 'SELECT * FROM `tiki_payment_requests` WHERE ' . $conditions;

		$all = $this->fetchAll( $data, array(), $max, $offset );

		return array(
			'cant' => $this->getOne( $count ),
			'data' => Perms::filter( array( 'type' => 'payment' ), 'object', $all, array( 'object' => 'paymentRequestId' ), 'payment_view' ),
		);
	}

	function get_outstanding( $offset, $max ) {
		return $this->get_payments( '`amount_paid` < `amount` AND NOW() <= `due_date` AND `cancel_date` IS NULL', $offset, $max );
	}

	function get_past( $offset, $max ) {
		return $this->get_payments( '`amount` <= `amount_paid` AND `cancel_date` IS NULL', $offset, $max );
	}

	function get_overdue( $offset, $max ) {
		return $this->get_payments( '`amount_paid` < `amount` AND NOW() > `due_date` AND `cancel_date` IS NULL', $offset, $max );
	}

	function get_canceled( $offset, $max ) {
		return $this->get_payments( '`cancel_date` IS NOT NULL', $offset, $max );
	}

	function cancel_payment( $id ) {
		$this->query( 'UPDATE `tiki_payment_requests` SET `cancel_date` = NOW() WHERE `paymentRequestId` = ?', array( $id ) );
	}

	function get_payment( $id ) {
		$info = reset( $this->fetchAll( 'SELECT * FROM `tiki_payment_requests` WHERE `paymentRequestId` = ?', array( $id ) ) );

		if( $info ) {
			$info['state'] = $this->find_state( $info );
			$info['amount_original'] = number_format( $info['amount'], 2, '.', ',' );
			$info['amount_remaining'] = number_format( $info['amount'] - $info['amount_paid'], 2, '.', ',' );

			$info['payments'] = array();
			$payments = $this->fetchAll( 'SELECT * FROM `tiki_payment_received` WHERE `paymentRequestId` = ? ORDER BY `payment_date` DESC', array( $id ) );

			foreach( $payments as $payment ) {
				$payment['details'] = json_decode( $payment['details'], true );
				$payment['amount_paid'] = number_format( $payment['amount'], 2, '.', ',' );
				$info['payments'][] = $payment;
			}

			return $info;
		}
	}

	private function find_state( $info ) {
		if( ! empty( $info['cancel_date'] ) ) {
			return 'canceled';
		}

		if( $info['amount_paid'] >= $info['amount'] ) {
			return 'past';
		}

		if( $info['due_date'] < date('Y-m-d H:i:s') ) {
			return 'overdue';
		}

		return 'outstanding';
	}

	function enter_payment( $invoice, $amount, $type, array $data ) {
		$data = json_encode( $data );

		$this->query( 'INSERT INTO `tiki_payment_received` ( `paymentRequestId`, `payment_date`, `amount`, `type`, `details` ) VALUES( ?, NOW(), ?, ?, ? )', array(
			$invoice, $amount, $type, $data
		) );
		$this->query( 'UPDATE `tiki_payment_requests` SET `amount_paid` = `amount_paid` + ? WHERE `paymentRequestId` = ?', array( $amount, $invoice ) );
	}
}

global $paymentlib;
$paymentlib = new PaymentLib;

