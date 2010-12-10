<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-webdav.php 27025 2010-05-09 13:23:41Z sept_7 $

require_once 'tiki-setup.php';

$access->check_feature('feature_webservices');

/**
 * Example of complex type
 */
class Tiki_ComplexType
{
	/**
	 * Param 1
	 * @var string
	 */
	var $param1;

	/**
	 * Param 2
	 * @var string
	 */
	var $param2;
}

/**
 * Write your SOAP webservices as methods of this class, it will be automagically
 * added to the WSDL file.
 *
 * Warning : while developing your web services, you should consider to set the
 * soap.wsdl_cache_enabled parameter to 0 in your php.ini.
 */
class Tiki_WebServices
{
	/**
	 * Prints some test data.
	 *
	 * @param string $param2
	 * @param string $param1
	 * @param string $param3
	 * @return string
	 */
	function test($param2, $param1, $param3)
	{
		return 'test1 ' . $param1 . ' test2 ' . $param2 . ' test3 ' . $param3;
	}

	/**
	 * Displays the Tiki_ComplexType data.
	 *
	 * @param Tiki_ComplexType $complex_param
	 * @return string
	 */
	function test_complex(Tiki_ComplexType $complex_param)
	{
		return $complex_param->param1 . ' =====> ' . $complex_param->param2;
	}
}

require_once 'lib/core/Zend/Soap/Server.php';
require_once 'lib/core/Zend/Soap/AutoDiscover.php';

if (is_null($_GET['wsdl'])) {
	$server = new Zend_Soap_Server($_SERVER['SCRIPT_URI'] . '?wsdl');
	$server->setClass('Tiki_WebServices');
	$server->handle();

} else {
	$wsdl = new Zend_Soap_AutoDiscover();
	$wsdl->setClass('Tiki_WebServices');
	$wsdl->handle();
}
