<?php

$secret = "49649f9dc6fe5f826dd3cc9f3f460cbd";
$partner_id = 23929;
$subp_id = 2392900;
$admin_secret = "6d36f37bdaebbf5297f9c89e6b12f7a1";
$service_url = "http://www.kaltura.com";
/*
$secret = "dae1be648b8a86d25adafdac2d32e8c3";
$partner_id = 250;
$subp_id = 25000;
//$admin_secret = "6d36f37bdaebbf5297f9c89e6b12f7a1";
*/

require_once ( "kalturaapi_php5_lib.php");
require_once ( "kaltura_helpers.php");
require_once ( "kaltura_client.php");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

?>
