<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This page should not load tiki-setup.php. Environment loading and access checking is done within the webservice.

require_once 'lib/videogals/watershedlib.php';
	
$server = new SoapServer ( "http://watershed-user.ustream.tv/webservice/watershed_user.php?wsdl", array( 
	'classmap' => Watershed_SoapServer::getClassMap(),
	'soap_version' => SOAP_1_2,
) );

$server->setClass( 'Watershed_SoapServer' );
$input = file_get_contents( 'php://input' );

// Debugging SOAP request
//$myFile = "soaplog.txt";
//$fh = fopen($myFile, 'a');
//fwrite($fh, $input);
//fclose($fh);

if ( $input ) {
	$server->handle( $input );
}

