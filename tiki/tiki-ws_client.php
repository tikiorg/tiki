<?php
include_once('tiki-setup.php');
require_once 'SOAP/Client.php';

$wsdl = new SOAP_WSDL ('http://localhost/tcvs/tiki/tiki-ws_server.php?wsdl');
$helloClient = $wsdl->getProxy();
$x = $helloClient->sayHello('Luis');
print_r($x);
?>
