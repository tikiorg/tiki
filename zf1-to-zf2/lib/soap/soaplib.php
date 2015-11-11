<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	die;
}

class Tiki_Soap
{
	private $cookies;
	public $allowCookies;	// boolean. If true, (session) cookies are handled

	function __construct() 
	{
		$this->cookies = array();
		$this->allowCookies = false;
	}

	/*
	*	If fullResponse = true, "out" parameters from .NET calls are included in the response. 
	*	If false, only the <request>Response part of the reply is included.
	*/	
	public function performRequest( $wsdl, $operation, $params, $options = array( 'encoding' => 'UTF-8' ), $fullReponse = false )
	{
		if (!extension_loaded('soap')) {
			return 'Extension SOAP not found';
		}

		if (!isset($options['soap_version'])) {
			$options['soap_version'] = SOAP_1_1;
		}

		$client = new Zend\Soap\Client($wsdl, $options);
		$soap_params = array();

		foreach ($params as $param_name => $param_value) {
			preg_match('/^(.*)\:(.*)$/', $param_name, $matches);

			if (count($matches) == 3) {
				if (!isset($soap_params[$matches[1]])) {
					$soap_params[$matches[1]] = array();
				}

				$soap_params[$matches[1]][$matches[2]] = $param_value;
			} else {
				$soap_params[$param_name] = $param_value;
			}
		}

		try {
			// Set (Session) cookies before the call
			if ($this->allowCookies) {
				if (is_array($this->cookies)) {
					foreach ($this->cookies as $cookieName => $cookieValue) { 
						$client->setCookie($cookieName, $cookieValue[0]);
					}
				}
			}

			// Perform the SOAP request
			$result = call_user_func_array(array($client, $operation), $soap_params);

			// Pick up any new cookies from the server
			if ($this->allowCookies) {
				$last_response = $client->getLastResponseHeaders();
				$soapClt = $client->getSoapClient();
				$this->cookies = array_merge($soapClt->_cookies, $this->cookies);
			}
			
		} catch (SoapFault $e) {
			trigger_error($e->getMessage());
			return $e->getMessage();
		}

		// Unless the full response result is specified, only reply the returned result, and not the "out" parameter results
		if (is_object($result) && !$fullReponse) {
			$result_name = $operation . 'Result';

			if (isset($result->$result_name)) {
				return $result->$result_name;
			}
		}

		return $result;
	}
}

global $soaplib;
$soaplib = new Tiki_Soap();
