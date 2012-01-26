<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_tracker_more_info.php 33195 2011-03-02 17:43:40Z changi67 $

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');

$access->check_feature('feature_trackers');

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

$access->check_permission('tiki_p_view_trackers');

$smarty->assign("info", $info);
$smarty->display("tiki-view_tracker_more_info.tpl");
