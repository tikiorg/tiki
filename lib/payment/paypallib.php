<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$response = $client->request('POST');

		$body = $response->getBody();

		return 'VERIFIED' === $body;
	}
}

global $paypallib;
$paypallib = new PaypalLib;

