<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-view_tracker_more_info.php,v 1.9 2007-10-12 07:55:33 nyloth Exp $
require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_trackers");
	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST["attId"])) {
	$smarty->assign('msg', tra("No item indicated"));
	$smarty->display("error.tpl");
	die;
}
$info = $trklib->get_moreinfo($_REQUEST["attId"]);
$trackerId = $info['trackerId'];
unset($info['trackerId']);
if (!$trackerId) {
	$smarty->assign('msg', tra("That tracker don't use extras."));
	$smarty->display("error_simple.tpl");
	die;
}
$smarty->assign('trackerId', $trackerId);
$tikilib->get_perm_object($trackerId, 'tracker');

if ($tiki_p_view_trackers != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign("info", $info);
$smarty->display("tiki-view_tracker_more_info.tpl");
