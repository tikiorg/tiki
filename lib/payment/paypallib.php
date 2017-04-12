<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PaypalLib extends TikiDb_Bridge
{
	function get_invoice( $ipn_data )
	{
		global $prefs;
		return isset( $ipn_data['invoice'] ) ? str_replace($prefs['payment_invoice_prefix'], '', $ipn_data['invoice']) : 0;
	}

	function get_amount( $ipn_data )
	{
		return $ipn_data['mc_gross'];
	}

	function is_valid( $ipn_data, $payment_info )
	{
		global $prefs;

		// Make sure this is not a fake, must be verified even if discarded, otherwise will be resent
		if ( ! $this->confirmed_by_paypal($ipn_data) ) {
			return false;
		}

		if ( ! is_array($payment_info) ) {
			return false;
		}

		// Skip other events
		if ( $ipn_data['payment_status'] != 'Completed' ) {
			return false;
		}

		// Make sure it is addressed to the right account
		if ( $ipn_data['receiver_email'] != $prefs['payment_paypal_business'] ) {
			return false;
		}

		// Require same currency
		if ( $ipn_data['mc_currency'] != $payment_info['currency'] ) {
			return false;
		}

		// Skip duplicate translactions
		foreach ( $payment_info['payments'] as $payment ) {
			if ( $payment['type'] == 'paypal' ) {
				if ( $payment['details']['txn_id'] == $ipn_data['txn_id'] ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Maps Tiki $languages to PayPal locales
	 *
	 * @param string $lang	tiki language value
	 * @return string		locale
	 */
	function localeMap ($lang)
	{

		$langMap = array(
			'ar' => 'en_AE',		// Arabic = United Arab Emirates - English ok?
			'bg' => 'en_BG',		// Bulgarian
			'ca' => '',				// Catalan
			'cn' => 'zh_C2',		// China - Simplified Chinese
			'cs' => 'en_CZ',		// Czech
			'cy' => 'un-uk',		// Welsh
			'da' => 'da_DK',		// Danish
			'de' => 'de_DE',		// Germany - German
			'en-uk' => 'en_GB',		// United Kingdom - English
			'en' => 'en_US',		// United States - English
			'es' => 'es_ES',		// Spain - Spanish
			'el' => 'en_GR',		// Greek
			'fa' => '',				// Farsi
			'fi' => 'en_FI',		// Finnish
			'fj' => 'en_FJ',		// Fijian
			'fr' => 'fr_FR',		// France - French
			'fy-NL' => 'nl_NL',		// Netherlands - Dutch (close enough?)
			'gl' => '',				// Galician
			'he' => 'he_IL',		// Israel - Hebrew
			'hr' => 'en_HR',		// Croatian
			'id' => 'en_ID',		// Indonesian
			'is' => 'en_IS',		// Icelandic
			'it' => 'it_IT',		// Italy - Italian
			'iu' => '',				// Inuktitut
			'iu-ro' => '',			// Inuktitut (Roman)
			'iu-iq' => '',			// Iniunnaqtun
			'ja' => 'ja_JP',		// Japan - Japanese
			'ko' => 'en_KR',		// Korean
			'hu' => 'en_HU',		// Hungarian
			'lt' => 'en_LT',		// Lithuanian
			'nds' => 'de_DE',		// Low German
			'nl' => 'nl_NL',		// Netherlands - Dutch
			'no' => 'no_NO',		// Norway - Norwegian
			'pl' => 'pl_PL',		// Poland - Polish
			'pt' => 'en_PT',		// Portuguese
			'pt-br' => 'pt_BR',		// Brazil - Portuguese
			'ro' => 'en_RO',		// Romanian
			'rm' => '',				// Romansh
			'ru' => 'ru_RU',		// Russia - Russian
			'sb' => 'en_SB',		// Pijin Solomon
			'si' => '',				// Sinhala
			'sk' => 'en_SK',		// Slovak
			'sl' => 'en_SI',		// Slovene
			'sq' => 'en_AL',		// Albanian
			'sr-latn' => '',		// Serbian Latin
			'sv' => 'sv_SE',		// Sweden - Swedish
			'tv' => 'en_TV',		// Tuvaluan
			'tr' => 'tr_TR',		// Turkey - Turkish
			'tw' => 'zh_TW',		// Taiwan - Traditional Chinese
			'uk' => 'en_UA',		// Ukrainian
			'vi' => 'en_VN',		// Vietnamese
		);

		return $langMap[$lang] ? $langMap[$lang] : 'en';
	}

	private function confirmed_by_paypal( $ipn_data )
	{
		global $prefs;

		$client = TikiLib::lib('tiki')->get_http_client();
		$client->setUri($prefs['payment_paypal_environment']);

		$base = array( 'cmd' => '_notify-validate' );

		$client->setParameterPost(array_merge($base, $ipn_data));
		$client->setMethod(Zend\Http\Request::METHOD_POST);
		$response = $client->send();

		$body = $response->getBody();

		return 'VERIFIED' === $body;
	}



    /**
     * Send HTTP POST Request
     *
     * @param    string    The API method name
     * @param    string    The POST Message fields in &name=value pair format
     * @return    array    Parsed HTTP Response body
     */
    function PayPalHttpPost($methodName_, $nvpStr_) {
        global $prefs;
        $environment = $prefs['payment_paypal_environment'];

        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($prefs['payment_paypal_business']);
        $API_Password = urlencode($prefs['payment_paypal_password']);
        $API_Signature = urlencode($prefs['payment_paypal_signature']);
        $API_Endpoint = "https://api-3t.paypal.com/nvp";
        if("sandbox" === $environment || "beta-sandbox" === $environment) {
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        }
        $version = urlencode('84.0');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if(!$httpResponse) {
            exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if(sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

    function PayPalLookupInvoice($invoiceId = "0", $startDate = "01/01/2010", $endDate = null) {
        // Set request-specific fields.
        //$transactionID = urlencode('example_transaction_id');
        $invoice = urlencode($invoiceId);

        // Add request-specific fields to the request string.
        //$nvpStr = "&TRANSACTIONID=$transactionID";
        $nvpStr = "&INVNUM=$invoice";

        //Here, by setting a proper STARTDATE:

        // Set additional request-specific fields and add them to the request string.
        if(!empty($startDate)) {
            $start_time = strtotime($startDate);
            $iso_start = date('Y-m-d\T00:00:00\Z',  $start_time);
            $nvpStr .= "&STARTDATE=$iso_start";
        }

        if(!empty($endDate)) {
            $end_time = strtotime($endDate);
            $iso_end = date('Y-m-d\T24:00:00\Z', $end_time);
            $nvpStr .= "&ENDDATE=$iso_end";
        }

        // Execute the API operation; see the PayPalHttpPost function above.
        return $this->PayPalHttpPost('TransactionSearch', $nvpStr);
    }
}

global $paypallib;
$paypallib = new PaypalLib;

