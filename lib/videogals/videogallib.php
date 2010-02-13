<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$secret = $prefs['secret'];
$partner_id = $prefs['partnerId'];
$subp_id = (int)$prefs['partnerId']*100;
$admin_secret = $prefs['adminSecret'];
$service_url = "http://www.kaltura.com";

require_once ( "KalturaClient_v3.php");

$mediaType = array("Any","Video","Image","Text","HTML","Audio","Video Remix","SHOW_XML","","Bubbles","XML","Document");

function kaltura_init_config ()
{
global $partner_id,$subp_id ,$secret ,$admin_secret , $service_url ;
	$conf = new KalturaConfiguration( $partner_id , $subp_id );
	$conf->partnerId = $partner_id;
	$conf->subPartnerId = $subp_id;
	$conf->secret = $secret;
	$conf->adminSecret = $admin_secret;
	$conf->serviceUrl = "http://www.kaltura.com";
	return $conf;
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
