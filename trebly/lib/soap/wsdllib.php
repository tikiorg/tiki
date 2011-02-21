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
		global $prefs;
		$parameters = array();

		if (!$wsdlUri || !$operation) {
			return $parameters;
		}

		$context = null;

		if ( $prefs['use_proxy'] == 'y' && !strpos($wsdlUri, 'localhost') ) {
			// Use proxy
			$context = stream_context_create(array(
								'http' => array(
									'proxy' => $prefs['proxy_host'] .':'. $prefs['proxy_port'], 
									'request_fulluri' => true)
							));
		}

		// Copy content in cache
		$wsdl_data = file_get_contents($wsdlUri, false, $context);

		if (!isset($wsdl_data) || empty($wsdl_data)) {
			trigger_error(tr("No WSDL found"));
			return array();
		}

		$wsdlFile = $GLOBALS['tikipath'] . 'temp/cache/' . md5($wsdlUri);
		file_put_contents( $wsdlFile, $wsdl_data );

		// Read wsdl from local copy
		$wsdl = new wsdl('file:' . $wsdlFile);

		if (!empty($wsdl->error_str)) {
			trigger_error($wsdl->error_str);
			return $parameters;
		}

		$data = $wsdl->getOperationData($operation);

		if (isset($data['input']['parts'])) {
			foreach ($data['input']['parts'] as $parameter => $type) {
				preg_match('/^(.*)\:(.*)\^?$/', $type, $matches);

				if (count($matches) == 3 && ($typeDef = $wsdl->getTypeDef($matches[2], $matches[1]))) {
					if (isset($typeDef['elements'])) {
						foreach ($typeDef['elements'] as $element) {
							$parameters[] = $typeDef['name'] . ':' . $element['name'];
						}
					}
				} else {
					$parameters[] = $parameter;
				}
			}
		}

		return $parameters;
	}
}

global $wsdllib;
$wsdllib = new Tiki_Wsdl();