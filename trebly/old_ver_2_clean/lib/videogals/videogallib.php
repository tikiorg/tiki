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

require_once ('lib/videogals/KalturaClient.php');

global $prefs, $kconf, $kclient, $ksession, $kuser, $url_host, $user, $tikilib, $smarty;

$access->check_feature('feature_kaltura');

$SESSION_ADMIN = 2;
$SESSION_USER = 0;
if (!empty($prefs['kuser'])) {
	$kuser = $prefs['kuser'];
} else {
	$kuser = $user;
}
if (empty($prefs['kServiceUrl'])) {
	$tikilib->set_preference('kServiceUrl', 'http://www.kaltura.com/');;
}
$smarty->assign('kServiceUrl', $prefs['kServiceUrl']);

if (empty($prefs['partnerId']) || !is_numeric($prefs['partnerId']) || empty($prefs['secret']) || empty($prefs['adminSecret'])) {
	$smarty->assign('msg', tra("You need to set your Kaltura account details: ") . '<a href="tiki-admin.php?page=video">' . tra('here') . '</a>');
	$smarty->display('error.tpl');
	die;
}
	
try {
	$kconf = new KalturaConfiguration($prefs['partnerId']);
	$kconf->serviceUrl = $prefs['kServiceUrl'];
	$kclient = new KalturaClient($kconf);
	$ksession = $kclient->session->start( $prefs['secret'], $kuser, $SESSION_USER,$prefs['partnerId'],86400,'edit:*' );
	$kclient->setKs($ksession);
	
} catch (Exception $e) {
	$smarty->assign('msg', tra('Could not establish Kaltura session. Try again') . '<br /><em>' . $e->getMessage() . '</em>');
	$smarty->display('error.tpl');
	die;
}




