<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		if ($paymentId != $jitGet->PreOrderID->digits()) {
			return false;
		}

		$hash = $this->generateHash($paymentId, $jitGet);
		if ($hash !== $jitGet->OKauthentication->word()) {
			return false;
		}

		return $this->checkWithService($paymentId);
	}

	public function capture_payment($payment, $received)
	{
		global $prefs;

		$url = $prefs['payment_israelpost_environment'] . 'genericJ4afterJ5?OpenAgent';
		$url .= '&' . http_build_query(array(
			'Business' => $prefs['payment_israelpost_business_id'],
			'PreOrderID' => $payment['paymentRequestId'],
			'cid' => $received['details']['CARTID'],
		), '', '&');

		$tikilib = TikiLib::lib('tiki');
		$out = $tikilib->httprequest($url);

		// All we care about is that the service received our request,
		// not if it worked. checkWithService will pull the truth.
		if ($out !== false) {
			$this->checkWithService($payment['paymentRequestId']);
			return true;
		}

		return false;
	}

	private function checkWithService($paymentId)
	{
		global $prefs;

		$client = $this->getClient();
		$response = $client->INQUIRE($prefs['payment_israelpost_business_id'], $prefs['payment_israelpost_api_password'], $paymentId);
		if (isset($response->ORDERS)) {
			$payment = $this->payment->get_payment($paymentId);
			// Collect the payment ids already entered
			$existingOrders = array_map(function ($payment) {
				return $payment['details']['ORDERID'];
			}, $payment['payments']);
			$existingAuth = array_map(function ($payment) {
				return $payment['details']['AUTHORISAT'];
			}, $payment['payments']);

			$entered = false;
			foreach ($response->ORDERS as $order) {
				if ($order->STATUS == 2) { // Order approved
					if (
						! in_array($order->ORDERID, $existingOrders) // Order not already entered
						&& $order->CURRENCY_CODE == $payment['currency'] // Same currency - we do not deal with conversions
					) {
						$this->payment->enter_payment($paymentId, $order->TOTAL_PAID, 'israelpost', (array) $order);
						$entered = true;
					}
				} elseif ($order->STATUS == 5) { // Pre-auth
					if (
						! in_array($order->AUTHORISAT, $existingAuth) // Order not already entered
						&& $order->CURRENCY_CODE == $payment['currency'] // Same currency - we do not deal with conversions
					) {
						$this->payment->enter_authorization($paymentId, 'israelpost', 3, (array) $order);
						$entered = true;
					}
				}
			}

			return $entered;
		}

		return false;
	}

	private function generateHash($paymentId, $jitGet)
	{
		global $prefs;

		$combined = array($prefs['payment_israelpost_business_id'], $prefs['payment_israelpost_api_password']);

		if ($prefs['payment_israelpost_request_preauth'] == 'y') {
			$combined[] = $jitGet->authorisat->digits();
		} else {
			$combined[] = $jitGet->OrderID->digits();
		}

		$combined[] = $jitGet->CartID->word();
		$combined[] = $paymentId;

		return hash("sha256", implode('', $combined));
	}

	private function getClient()
	{
		global $prefs;
		
		$wsdl = $prefs['payment_israelpost_environment'] . 'GetGenericStatus?wsdl';
		$client = new Zend\Soap\Client($wsdl, array(
			'soap_version' => SOAP_1_1,
		));

		return $client;
	}
}

