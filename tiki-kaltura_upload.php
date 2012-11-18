<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';

$auto_query_args = array();

$access->check_feature('feature_kaltura');
$access->check_permission(array('tiki_p_upload_videos'));
//get_strings tra('Upload Media')

require_once 'lib/videogals/kalturalib.php';

$cwflashVars = array();
if ($user) {
	$cwflashVars["uid"]               = $user;
} else {
	$cwflashVars["uid"]               = 'Anonymous';
}
$cwflashVars["partnerId"]         = $prefs['kaltura_partnerId'];
$cwflashVars["ks"]                = $kalturalib->session;
$cwflashVars["afterAddEntry"]     = "afterAddEntry";
$cwflashVars["close"]             = "onContributionWizardClose";
$cwflashVars["showCloseButton"]   = false;
$cwflashVars["Permissions"]       = 1;		// 1=public, 2=private, 3=group, 4=friends

$smarty->assign_by_ref('cwflashVars', json_encode($cwflashVars));

$count = 0;
if ($_REQUEST['kcw']) {
	$count = count($_REQUEST['entryId']);
	$smarty->assign_by_ref('count', $count);
}
// Display the template
if (isset($_REQUEST['full']) && $_REQUEST['full'] === 'n') {

	$smarty->assign('mid', 'tiki-kaltura_upload.tpl');
	$smarty->display("tiki_full.tpl");

} else {
	$smarty->assign('mid', 'tiki-kaltura_upload.tpl');
	$smarty->display("tiki.tpl");
}
