<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
$access->check_feature('feature_trackers');
$auto_query_args = array('trackerId');

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}
$objectperms = Perms::get('tracker', $_REQUEST['trackerId']);
$smarty->assign('permsType', $objectperms->from());

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
if (!empty($_REQUEST['trackerId'])) {
	$smarty->assign('trackerInfo', $trklib->get_tracker($_REQUEST['trackerId']));
}

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
$trackers = $trklib->list_trackers($offset, $maxRecords, $sort_mode, $find, true);

foreach ($trackers["data"] as &$tracker) {
	if ($userlib->object_has_one_permission($tracker["trackerId"], 'tracker')) {
		$tracker["individual"] = 'y';
	} else {
		$tracker["individual"] = 'n';
	}
}

$smarty->assign_by_ref('cant', $trackers['cant']);
$smarty->assign_by_ref('trackers', $trackers["data"]);

$urlquery['find'] = $find;
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
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
