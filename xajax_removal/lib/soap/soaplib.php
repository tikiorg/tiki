<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ajaxlib.php 29493 2010-09-21 16:33:19Z jonnybradley $

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

		$options['soap_version'] = SOAP_1_2;
		$client = new Zend_Soap_Client( $wsdl, $options );

		try {
			$result = call_user_func_array(array($client, $operation), $params);

		} catch (SoapFault $e) {
			return $e->getMessage();
		}

		if (is_object($result)) {
			$result_name = $operation . 'Result';
			return $result->$result_name;
		}

		return $result;
	}
}

global $soaplib;
$soaplib = new Tiki_Soap();
