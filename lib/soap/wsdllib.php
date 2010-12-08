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

require_once 'nusoap/nusoap.php';

class Tiki_Wsdl
{
	public function getParametersNames( $wsdlUri, $operation )
	{
		$parameters = array();

		if (!$wsdlUri || !$operation) {
			return $parameters;
		}

		$wsdl = new wsdl($wsdlUri);
		$data = $wsdl->getOperationData($operation);


		if (isset($data['input']['parts'])) {
			foreach ($data['input']['parts'] as $parameter => $type) {
				$parameters[] = $parameter;
			}
		}

		return $parameters;
	}
}

global $wsdllib;
$wsdllib = new Tiki_Wsdl();