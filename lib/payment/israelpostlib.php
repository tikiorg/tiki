<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class IsraelPostLib
{
	private $payment;

	function __construct(PaymentLib $payment)
	{
		$this->payment = $payment;
	}

	/**
	 * Check if the payment has been received through the gateway's API.
	 * Return false if this is not supported.
	 */
	public function check_payment($paymentId, $jitGet, $jitPost)
	{
		global $prefs;
		if ($paymentId != $jitGet->PreOrderID->digits()) {
			return false;
		}

		$combined = $prefs['payment_israelpost_business_id'] . $prefs['payment_israelpost_api_password'] . $jitGet->OrderID->digits() . $jitGet->CartID->word() . $paymentId;
		if (hash("sha256", $combined) !== $jitGet->OKauthentication->word()) {
			return false;
		}

		$wsdl = $prefs['payment_israelpost_environment'] . 'GetGenericStatus?wsdl';
		$client = new Zend_Soap_Client($wsdl, array(
			'soap_version' => SOAP_1_1,
		));
		$response = $client->INQUIRE($prefs['payment_israelpost_business_id'], $prefs['payment_israelpost_api_password'], $paymentId);
		if (isset($response->ORDERS)) {
			$payment = $this->payment->get_payment($paymentId);
			// Collect the payment ids already entered
			$existing = array_map(function ($payment) {
				return $payment['details']['ORDERID'];
			}, $payment['payments']);

			$entered = false;
			foreach ($response->ORDERS as $order) {
				if ($order->STATUS == 2 // Order approved
					&& ! in_array($order->ORDERID, $existing) // Order not already entered
					&& $order->CURRENCY_CODE == $payment['currency'] // Same currency - we do not deal with conversions
				) {
					$this->payment->enter_payment($paymentId, $order->TOTAL_PAID, 'israelpost', (array) $order);
					$entered = true;
				}
			}

			return $entered;
		}

		return false;
	}
}

