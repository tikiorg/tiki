<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PaypalLib extends TikiDb_Bridge
{
	function get_invoice( $ipn_data ) {
		global $prefs;
		return isset( $ipn_data['invoice'] ) ? str_replace( $prefs['payment_invoice_prefix'], '', $ipn_data['invoice'] ) : 0;
	}

	function get_amount( $ipn_data ) {
		return $ipn_data['mc_gross'];
	}

	function is_valid( $ipn_data, $payment_info ) {
		global $prefs;

		// Make sure this is not a fake, must be verified even if discarded, otherwise will be resent
		if( ! $this->confirmed_by_paypal( $ipn_data ) ) {
			return false;
		}

		if( ! is_array( $payment_info ) ) {
			return false;
		}

		// Skip other events
		if( $ipn_data['payment_status'] != 'Completed' ) {
			return false;
		}

		// Make sure it is addressed to the right account
		if( $ipn_data['receiver_email'] != $prefs['payment_paypal_business'] ) {
			return false;
		}

		// Require same currency
		if( $ipn_data['mc_currency'] != $payment_info['currency'] ) {
			return false;
		}

		// Skip duplicate translactions
		foreach( $payment_info['payments'] as $payment ) {
			if( $payment['type'] == 'paypal' ) {
				if( $payment['details']['txn_id'] == $ipn_data['txn_id'] ) {
					return false;
				}
			}
		}

		return true;
	}

	private function confirmed_by_paypal( $ipn_data ) {
		global $prefs;

		$client = TikiLib::lib('tiki')->get_http_client();
		$client->setUri( $prefs['payment_paypal_environment'] );

		$base = array( 'cmd' => '_notify-validate' );  

		$client->setParameterPost( array_merge( $base, $ipn_data ) );
		$response = $client->request( 'POST' );

		$body = $response->getBody();

		return 'VERIFIED' === $body;
	}
}

global $paypallib;
$paypallib = new PaypalLib;

