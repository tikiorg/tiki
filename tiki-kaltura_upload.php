<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('feature_kaltura');

include_once ("lib/videogals/KalturaClient_v3.php");

$access->check_permission(array('tiki_p_upload_videos'));

$secret = $prefs['secret'];
$admin_secret = $prefs['adminSecret'];
$partner_id = $prefs['partnerId'];
$SESSION_ADMIN = 2;
$SESSION_USER = 0;

if (empty($partner_id) || !is_numeric($partner_id) || empty($secret) || empty($admin_secret)) {
	$smarty->assign('msg', tra("You need to set your Kaltura account details: ") . '<a href="tiki-admin.php?page=kaltura">' . tra('here') . '</a>');
	$smarty->display('error.tpl');
	die;
}
$smarty->assign('headtitle', tra('Kalture Upload'));

try {
	$kconf = new KalturaConfiguration($partner_id);
	$kclient = new KalturaClient($kconf);
	$ksession = $kclient->session->start($secret,$user,$SESSION_USER);
} catch (Exception $e) {
	$smarty->assign('msg', tra('Could not establish Kaltura session. Try again') . '<br /><em>' . $e->getMessage() . '</em>');
	$smarty->display('error.tpl');
	die;
}
$kclient->setKs($ksession);

$cwflashVars = array();
$cwflashVars["uid"]               = $user;
$cwflashVars["partnerId"]         = $partner_id;
$cwflashVars["ks"]                = $ksession;
$cwflashVars["afterAddEntry"]     = "afterAddEntry";
$cwflashVars["close"]             = "onContributionWizardClose";
$cwflashVars["showCloseButton"]   = false;
$cwflashVars["Permissions"]       = 1;		// 1=public, 2=private, 3=group, 4=friends

$smarty->assign_by_ref('cwflashVars',json_encode($cwflashVars));

$count = 0;
if($_REQUEST['kcw']){
	$count = count($_REQUEST['entryId']);
	$smarty->assign_by_ref('count',$count);
}
// Display the template
$smarty->assign('mid','tiki-kaltura_upload.tpl');
$smarty->display("tiki.tpl");
