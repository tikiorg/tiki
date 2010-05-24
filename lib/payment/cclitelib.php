<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: cclitelib.php 25244 2010-02-16 06:26:12Z changi67 $

class CCLiteLib extends TikiDb_Bridge
{
	function get_invoice( $ipn_data ) {
		return isset( $ipn_data['invoice'] ) ? $ipn_data['invoice'] : 0;
	}

	function get_amount( $ipn_data ) {
		return $ipn_data['mc_gross'];
	}

	function is_valid( $ipn_data, $payment_info ) {
		global $prefs;

		// Make sure this is not a fake, must be verified even if discarded, otherwise will be resent
		if( ! $this->confirmed_by_cclite( $ipn_data ) ) {
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

	private function confirmed_by_cclite( $ipn_data ) {
		global $prefs;
		
		return true;	// for now TODO

		require_once 'lib/core/lib/Zend/Http/Client.php';
		$client = new Zend_Http_Client( $prefs['payment_cclite_environment'] );

		$base = array( 'cmd' => '_notify-validate' );  

		$client->setParameterPost( array_merge( $base, $ipn_data ) );
		$response = $client->request( 'POST' );

		$body = $response->getBody();

		return 'VERIFIED' === $body;
	}
	
	function pay_invoice($invoice, $amount, $currency = '') {
		global $user, $prefs;
		
		if (empty($currency)) {
			$currency = $prefs['payment_currency'];
		}
		$res = $this->cclite_contents('pay', $prefs['payment_cclite_merchant_user'], $prefs['payment_cclite_registry'], $amount, $currency);
		
		return $res;
	}
	
	/**
	 * Adapted from cclite 0.7 drupal gateway (example)
	 * @arg_list[0] = mode {recentsummary|block|pay|adduser|modifyuser}
	 * @arg_list[1] = user
	 * @arg_list[2] = registry
	 * @arg_list[3] = ammount
	 * @arg_list[4] = currency
	 * @arg_list[5] = other?
	 * 
	 * @return ??
	 */
	private function cclite_contents() {
	    global $user, $prefs;
	    $username = $user;
	    $arg_list = func_get_args();
	    $numargs = count($arg_list);
	    $block_content = '';
	    $cclite_operation = '';
	    //  debug arguments passed
	    $stuff = "|" . implode("-", $arg_list) . "|";
	    // construct the payment url from configuration information
	    $cclite_base_url = $prefs['payment_cclite_gateway'];
	    $c_url = '';
	    $ch = curl_init();
	    //log_debug("operation code", $arg_list[0]);
	    if ($arg_list[0] != 'adduser') {
	        $logon_result = $this->cclite_remote_logon();
	        //     log_debug("logon result $logon_result[0]  $logon_result[1]" );
	        if (strlen($logon_result[1])) {
	            curl_setopt($ch, CURLOPT_COOKIE, $logon_result[1]);
	        } else {
	            return;
	        }
	    }
	    //log_debug("logon result 1", $logon_result[1]);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	    curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	    curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_FRESH_CONNECT, FALSE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	    // this switch statement needs to map to the Rewrites in the cclite .htaccess file, so if you're
	    // doing something custom-made, you need to think about:
	    // -here-, .htaccess and various bits of login in the cclite motor
	    switch ($arg_list[0]) {
	        case 'recent':
	            // $block_content = "case recent transactions : $arg_list[0]/$arg_list[1]" ;
	            $c_url = "$cclite_base_url/recent/transactions";
	        break;
	        case 'summary':
	            // $block_content = "case summary : $arg_list[0]  $arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]/$arg_list[5]" ;
	            $c_url = "$cclite_base_url/summary";
	        break;
	
	        case 'block':
	            // $block_content = "case summary : $arg_list[0]  $arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]/$arg_list[5]" ;
	            $c_url = "$cclite_base_url/summary";
	        break;
	
	        case 'pay':
	            //if (! user_access('make payments')) {
	            //    return "$username not authorised to make payments" ;
	            //}
	            // pay/test1/dalston/23/hack(s) using the merchant key
	            //  $block_content = "case pay : $cclite_base_url/$arg_list[0]/$arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]" ;
	            $c_url = "$cclite_base_url/pay/$arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]";
            break;
	        case 'adduser':
	            // direct/adduser/dalston/test1/email using the merchant key, without using individual logon
	            //log_debug("in adduser ", "$cclite_base_url/direct/adduser/$arg_list[1]/$arg_list[2]/$arg_list[3]");
	            $c_url = "$cclite_base_url/direct/adduser/$arg_list[1]/$arg_list[2]/$arg_list[3]";
	        break;
	        case 'modifyuser':
	            // direct/modifyuser/dalston/test1/email using the merchant key, without using individual logon
	            // non-working at present...
	            //log_debug("in modifyuser ", "$cclite_base_url/direct/modifyuser/$arg_list[1]/$arg_list[2]/$arg_list[3]");
	            $c_url = "$cclite_base_url/direct/modifyuser/$arg_list[1]/$arg_list[2]/$arg_list[3]";
	        break;
	        case 'debit':
	        	// check perms here TODO
	            //if (! user_access('make payments')) {
	            //    return "$username not authorised to make payments" ;
	            //}
	            //  $block_content = "case pay : $cclite_base_url/$arg_list[0]/$arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]" ;
	            $c_url = "$cclite_base_url/debit/$arg_list[1]/$arg_list[2]/$arg_list[3]/$arg_list[4]";
			break;
	        // nothing to display in 
	        default:
	           return "No cclite function selected use <a title=\"cclite passthrough help\" href=\"/$cclite_base_url/help\">help</a>" ;
	    }
	    curl_setopt($ch, CURLOPT_URL, $c_url);
	    $block_content = curl_exec($ch);
	    curl_close($ch);
	    // $block_content = $cclite_base_url ;
	    return $block_content;
	}
	
	/**
	 * Modified from cclite 0.7 gateway various examples
	 * 
	 * @return multitype:mixed string |multitype:string 
	 */
	private function cclite_remote_logon() {
	    global $user, $prefs;
	    $username = $user;
	    // not worth trying if no drupal user name
	    if (strlen($username)) {
	    	
	    	$params = $this->get_gateway_variables();
	        $api_hash = hash( $params['hashtype'] , ( $params['key'] . $_SERVER['SERVER_ADDR']), 'true');
	        $api_hash = hash( $params['hashtype'] , ( $params['key'] . '82.43.200.105'), 'true');
	        $api_hash = CCLiteLib::urlsafe_b64encode($api_hash);
	        // payment url from configuration information
	        $cclite_base_url = $params['gateway'];
	        $registry = $params['registry'];
	        $REST_url = "$cclite_base_url/logon/$username/$registry";	// /$api_hash
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	        curl_setopt($ch, CURLOPT_COOKIE, "merchant_key_hash=$api_hash");
	        curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	        curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
	        curl_setopt($ch, CURLOPT_HEADER, TRUE);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	        curl_setopt($ch, CURLOPT_URL, $REST_url);
	        $logon = curl_exec($ch);
	        curl_close($ch);
	        preg_match_all('|Set-Cookie: (.*);|U', $logon, $results);
	        $cookies = implode("; ", $results[1]);
	        return array($logon, $cookies);
	    } else {
	        return array('failed', $cookies);
	    }
	}
	
	private function get_gateway_variables () {
	    global $user, $prefs;
		
		$gateway = $prefs['payment_cclite_gateway'];
		$key = $prefs['payment_cclite_merchant_key'];
		$hashtype = $prefs['payment_cclite_hashing_algorithm'];
		$registry = $prefs['payment_cclite_registry'];
		$limit = 5 ; //for the moment,five records
		 
		$values = array('user'=> $user,
			'limit' => $limit,
			'gateway' => $gateway,
			'key'=> $key,
			'hashtype'=> $hashtype,
			'registry'=> $registry
		);
		
		 return $values ;
	}
	
// used to transport merchant key hash - probably duplicates of tiki fns REFACTOR
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

