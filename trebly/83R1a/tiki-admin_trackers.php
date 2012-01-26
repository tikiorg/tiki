<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_trackers.php 37848 2011-10-01 18:18:38Z changi67 $

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
$access->check_feature('feature_trackers');
$auto_query_args = array('trackerId');

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}
$objectperms = Perms::get('tracker', $_REQUEST['trackerId']);
if (!$objectperms->admin_trackers) {
	$access->display_error('', tra('Permission denied').": ". 'tiki_p_admin_trackers', '403');
}
$smarty->assign('permsType', $objectperms->from());

if (!empty($_REQUEST['exportTrackerProfile']) && !empty($_REQUEST['trackerId'])) {
	include_once('lib/profilelib/installlib.php');
	$prof = new Tiki_Profile_InstallHandler_Tracker();
	$res = $prof->_export($_REQUEST['trackerId']);
	header("Content-type: text/yaml");
	header('Content-Disposition: attachment; filename=tracker_'.$_REQUEST['trackerId'].'.yaml');
	header('Expires: 0');
	header('Pragma: public');
	echo $res;
	die;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$info = array();

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$channels = $trklib->list_trackers($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('trackers', $channels['data']);
$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["trackerId"], 'tracker')) {
		$channels["data"][$i]["individual"] = 'y';
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}
$urlquery['find'] = $find;
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('uses_tabs', 'y');

ask_ticket('admin-trackers');
global $wikilib;
include_once ('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'trackerDescription');
$smarty->assign_by_ref('plugins', $plugins);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_trackers.tpl');
$smarty->display("tiki.tpl");
