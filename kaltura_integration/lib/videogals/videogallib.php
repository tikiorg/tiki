<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/videogals/videogallib.php,v 1.97.2.4 2008-03-06 19:45:42 sampaioprimo Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$secret = "49649f9dc6fe5f826dd3cc9f3f460cbd";
$partner_id = 23929;
$subp_id = 2392900;
$admin_secret = "6d36f37bdaebbf5297f9c89e6b12f7a1";
$service_url = "http://www.kaltura.com";

require_once ( "kaltura_client.php");
function kaltura_init_config ()
{
global $partner_id,$subp_id ,$secret ,$admin_secret , $service_url ;
	$conf = new KalturaConfiguration( $partner_id , $subp_id );
	$conf->partnerId = $partner_id;
	$conf->subPartnerId = $subp_id;
	$conf->secret = $secret;
	$conf->adminSecret = $admin_secret;
	$conf->serviceUrl = "http://www.kaltura.com";
	//$conf->setLogger( new KalturaDemoLogger());
	return $conf;
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past





