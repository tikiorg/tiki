<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');

require_once 'SOAP/Client.php';

$wsdl = new SOAP_WSDL('http://localhost/tcvs/tiki/tiki-ws_server.php?wsdl');
$helloClient = $wsdl->getProxy();
$x = $helloClient->sayHello('Luis');
print_r ($x);

?>