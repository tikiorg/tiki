<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	die;
}

require_once('lib/core/Zend/Soap/Client.php');
require_once('lib/core/Zend/Soap/Wsdl.php');

class Tiki_Soap
{
	public function performRequest( $wsdl, $operation, $params, $options = array( 'encoding' => 'UTF-8' ) )
	{
		if (!extension_loaded('soap')) {
			return 'Extension SOAP not found';
		}

		if (!isset($options['soap_version'])) {
			$options['soap_version'] = SOAP_1_1;
		}

		$client = new Zend_Soap_Client( $wsdl, $options );
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
			$result = call_user_func_array(array($client, $operation), $soap_params);

		} catch (SoapFault $e) {
			trigger_error($e->getMessage());
			return $e->getMessage();
		}

		if (is_object($result)) {
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
