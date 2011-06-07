<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class CCLiteLib extends TikiDb_Bridge
{
	// member vars (defaults from prefs)
	private $gateway;
	private $key_hash;
	private $registries;
	private $currencies;
	private $merchant_user;
	
	function __construct() {
		global $prefs, $access;
		// check for essential prefs
		$access->check_feature('payment_feature');
		// need to add a check for empty, not just y/n - TODO one day
		//$access->check_feature('payment_cclite_registries');
		//$access->check_feature('payment_cclite_gateway');
		//$access->check_feature('payment_cclite_merchant_key');
		
		$this->gateway = rtrim($prefs['payment_cclite_gateway'], '/');
		$this->registries = unserialize( $prefs['payment_cclite_registries'] );
		$this->currencies = unserialize($prefs['payment_cclite_currencies']);
		$this->merchant_user = $prefs['payment_cclite_merchant_user'];
				
		if (($prefs['payment_cclite_mode'] == 'test' && $_SERVER['SERVER_ADDR'] != '127.0.0.1' && $_SERVER['SERVER_ADDR'] != '::1') || empty($prefs['payment_cclite_test_ip'])) {
			$ip = $_SERVER['SERVER_ADDR'];
		} else {
			// debug SERVER_ADDR for local testing on NAT'ed server
			$ip = $prefs['payment_cclite_test_ip'];
		}
		$api_hash = hash( $prefs['payment_cclite_hashing_algorithm'] , ( $prefs['payment_cclite_merchant_key'] . $ip), 'true');
		$this->key_hash = CCLiteLib::urlsafe_b64encode($api_hash);
	}
	
	public function get_registries() {
		return $this->registries;
	}
	
	public function get_registry() {
		if (!empty($this->registries)) {
			return $this->registries[0];	// default if not specified in plugins etc
		} else {
			global $access, $page;
			$access->display_error($page, tra('Cclite error'), '500', true, tra('No registries specified in admin/payment/cclite.'));
		}
	}
	
	public function get_currencies() {
		return $this->currencies;
	}
	
	/**
	 * @param string $reg Registry to find currency for (uses registries[0] if not specified)
	 */
	public function get_currency($reg = '') {
		global $prefs;
		
		if (empty($reg)) {
			$reg = $this->get_registry();
		}
		
		$i = array_search($reg, $this->registries);
		
		if ($i !== false) {
			return $this->currencies[$i];
		} else {
			return $prefs['payment_currency'];
		}
	}
	
	public function get_invoice( $ipn_data ) {
		return isset( $ipn_data['invoice'] ) ? $ipn_data['invoice'] : 0;
	}

	public function get_amount( $ipn_data ) {
		return $ipn_data['mc_gross'];
	}

	public function is_valid( $ipn_data, $payment_info ) {
		global $prefs;

		if( ! is_array( $payment_info ) ) {
			return false;
		}

		// Skip other events
		if( $ipn_data['payment_status'] != 'Completed' ) {
			return false;
		}

		// Make sure it is addressed to the right account
		if( $ipn_data['receiver_email'] != $prefs['payment_cclite_business'] ) {
			return false;
		}

		// Require same currency
		if( $ipn_data['mc_currency'] != $payment_info['currency'] ) {
			return false;
		}

		// Skip duplicate translactions
		foreach( $payment_info['payments'] as $payment ) {
			if( $payment['type'] == 'cclite' ) {
				if( $payment['details']['txn_id'] == $ipn_data['txn_id'] ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * This function just calls $paymentlib->enter_payment() which then triggers the behaviours
	 * The behaviours the do the actual transfer of currency
	 * 
	 * @param int $invoice
	 * @param decimal $amount
	 * @param string $currency
	 * @param string $registry
	 * @param string $source_user
	 * 
	 * @return string result from cclite
	 */
	public function pay_invoice($invoice, $amount, $currency = '', $registry = '', $source_user = '') {
		global $user, $prefs, $paymentlib, $tikilib;
		require_once 'lib/payment/paymentlib.php';
		
		$msg = tr('Cclite payment initiated on %0', $tikilib->get_short_datetime($tikilib->now));
		
		$paymentlib->enter_payment( $invoice, $amount, 'cclite', array('info' => $msg));
		
		return $msg;
	}

	/**
	 * Pays $amount from logged in (or source_user TODO) user to manager account
	 * 
	 * @param int $invoice
	 * @param decimal $amount
	 * @param string $currency
	 * @param string $registry
	 * @param string $destination_user
	 * 
	 * @return string result from cclite
	 */
	public function pay_user( $amount, $currency = '', $registry = '', $destination_user = '', $source_user = '') {
		global $user, $prefs, $paymentlib;
		require_once 'lib/payment/paymentlib.php';
		if (empty($source_user)) {
			$source_user = $this->merchant_user;
		}
		
		$res = $this->cclite_send_request('pay', $destination_user, $registry, $amount, $currency, $source_user);
		
//		if (strpos($res, 'Transaction Accepted') !== false) {	// e.g. "Transaction Accepted<br/>Ref:&nbsp;hpnUKZZ4BMG4IXDHVmfxXdubtsk"
//			$paymentlib->enter_payment( $invoice, $amount, 'cclite', array($res) );
//		}
		$r = $this->cclite_send_request('logoff');
		
		return $res;
	}

	/**
	 * Adapted from cclite 0.7 drupal gateway (example)
	 * 
	 * @command		recent|summary|pay|adduser|modifyuser|debit
	 * @other_user	destination for payment - uses merchant_user if empty
	 * @registry	cclite registry
	 * @amount		amount (decimal/float for cost, or email for adduser command)
	 * @currency	currency (same as currency "name" in cclite (not "code" yet)
	 * 				defaults to registry currency
	 * @main_user	source of payment - uses logged in user if empty
	 * 
	 * @return		result from cclite server (html hopefully)
	 */

	function cclite_send_request( $command, $other_user = '', $registry = '', $amount = 0, $currency = '', $main_user = '') {
		global $user, $prefs;
		
		if (empty($other_user)) { $other_user = $this->merchant_user; }
		if (empty($main_user)) { $main_user = $user; }
		if (empty($registry)) { $registry = $this->get_registry(); }
		if (empty($currency)) { $currency = $this->get_currency( $registry ); }
		
		$result = '';

		// construct the payment url from configuration information
		$cclite_base_url = $this->gateway;
		$REST_url = '';
		$ch = curl_init();
		if ($command != 'adduser') {
			$logon_result = $this->cclite_remote_logon($main_user, $registry);
			if ($logon_result[0] != 'failed' && strlen($logon_result[1])) {
				curl_setopt($ch, CURLOPT_COOKIE, $logon_result[1]);
			} else {
				return tr('Connection to cclite server %0 failed for %1<br />"%2"', $cclite_base_url, $main_user, $logon_result[1]);
			}
		}
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		//curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

		//curl_setopt($ch, CURLOPT_VERBOSE, true);

		// this switch statement needs to map to the Rewrites in the cclite .htaccess file, so if you're
		// doing something custom-made, you need to think about:
		// -here-, .htaccess and various bits of login in the cclite motor
		switch ($command) {
			case 'recent':
				$REST_url = "$cclite_base_url/recent/transactions";
				break;
			case 'summary':
				$REST_url = "$cclite_base_url/summary";
				break;
			case 'pay':
				//if (! user_access('make payments')) {
				//    return "$username not authorised to make payments" ;
				//}
				// pay/test1/dalston/23/hack(s) using the merchant key
				$REST_url = "$cclite_base_url/pay/$other_user/$registry/$amount/$currency";
				break;
		case 'adduser':
			// direct/adduser/dalston/test1/email using the merchant key, without using individual logon
			// email passed in as $amount
			$REST_url = "$cclite_base_url/direct/adduser/$registry/" . urlencode($other_user . '/' . $amount);
			curl_setopt($ch, CURLOPT_COOKIE, 'merchant_key_hash=' . $this->key_hash);
			break;
		case 'modifyuser':
			// direct/modifyuser/dalston/test1/email using the merchant key, without using individual logon
			// non-working at present...
			$REST_url = "$cclite_base_url/direct/modifyuser/$registry/" . urlencode($other_user . '/' . $amount);
			curl_setopt($ch, CURLOPT_COOKIE, 'merchant_key_hash=' . $this->key_hash);
			break;
		case 'debit':
			// non-working at present...
			$REST_url = "$cclite_base_url/debit/$other_user/$registry/$amount/$currency";
			break;
		case 'logoff':
			// non-working at present...
			$REST_url = "$cclite_base_url/logoff";
			break;
		default:
			return "No cclite function selected use <a title=\"cclite passthrough help\" href=\"/$cclite_base_url/help\">help</a>" ;
		}
		curl_setopt($ch, CURLOPT_URL, $REST_url);
		$result = curl_exec($ch);
		curl_close($ch);
		return strip_tags($result);
	}

	/**
	 * Modified from cclite 0.7 gateway various examples
	 *
	 * @return multitype:mixed string |multitype:string
	 */
	private function cclite_remote_logon($username = '', $registry = '') {
		global $user, $prefs, $userlib;
		
		if (empty($username)) { $username = $user; }
		
		// not worth trying if no user name
		if (!empty($username)) {
			
			if (empty($registry)) { $registry = $this->get_registry(); }
			$cclite_base_url = $this->gateway;
			
			// payment url from configuration information
			$REST_url = "$cclite_base_url/logon/$username/$registry";	// /$api_hash
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_COOKIE, 'merchant_key_hash=' . $this->key_hash);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $REST_url);
			
//			curl_setopt($ch, CURLOPT_VERBOSE, true);
			
			$logon = curl_exec($ch);
			curl_close($ch);
			
			$results = array();	// for response & cookies on success
			$err_msg = '';		// error message on failure
			
			if ($logon) {
				// e.g. login failed for jonny_tiki at c2c1:  <a href="http://c2c.ourproject.org/cgi-bin/cclite.cgi">Try again?</a>
				if (preg_match('/^(login failed for '.$username.'.*'.$registry.'[^<]*)/mi', $logon, $results)) {	// no user there?
					$email = $userlib->get_user_email($username);
					if ($email) {	// required
						$res = $this->cclite_send_request('adduser', $username, $registry, $email);	// not currently working cclite 0.7.0
						if ($res && !preg_match('/404 Not Found/', $res)) {							// seems to return a 404 :(
							$logon = curl_exec($ch);	// retry login
						} else {
							$err_msg = trim($results[0]);
							$logon = 'failed';
						}
					}
				}
				// e.g. test_user at test_reg is not active: confirm or contact the administrator <a href="http://c2c.ourproject.org/cgi-bin/cclite.cgi">Try again?</a>
				if (preg_match('/^(.*?'.$username.'.*'.$registry.'[^<]*)/mi', $logon, $results)) {		// check for other errors & remove cclite link
					$err_msg = trim($results[0]);
					$logon = 'failed';	// error in $results[0]
				} else if (preg_match('/HTTP\/1.1 302/mis', $logon) && preg_match('/<BODY.*?>(.*)<\/BODY>/mis', $logon, $results)) {
					$err_msg = trim(strip_tags($results[0], '<br />'));
					//$logon = 'failed';
				}
			}
			if ($logon && $logon != 'failed') {
				preg_match_all('|Set-Cookie: (.*);|U', $logon, $results);
				$cookies = implode("; ", $results[1]);
				return array($logon, $cookies);
			}
		} else {
			$err_msg = 'No result from cclite server.';
		}
		// fall through failed
		return array('failed', $err_msg);
	}

	// used to transport merchant key hash - probably duplicates of tiki fns REFACTOR?
	static function urlsafe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}
	static function urlsafe_b64decode($string) {
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data.= substr('====', $mod4);
		}
		return base64_decode($data);
	}

}

global $cclitelib;
$cclitelib = new CCLiteLib;

