<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'trackers';
require_once ('tiki-setup.php');
$trklib = TikiLib::lib('trk');
$access->check_feature('feature_trackers');
$auto_query_args = array('sort_mode', 'offset', 'find');

// Only used to call an edit dialog directly from other pages
$auto_query_args = array('trackerId');
if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}
if (!empty($_REQUEST['trackerId'])) {
	$smarty->assign('trackerInfo', $trklib->get_tracker($_REQUEST['trackerId']));
}
$smarty->assign('trackerId', $_REQUEST["trackerId"]);

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
	
	$tracker['watched'] = $user && $tikilib->user_watches($user, 'tracker_modified', $tracker["trackerId"], 'tracker');
	
	// Could be used with object_perms_summary.tpl instead of the above but may be less performant
	//	$objectperms = Perms::get('tracker', trackerId);
	//	$smarty->assign('permsType', $objectperms->from());

	if ($tiki_p_admin_trackers !== 'y') {
		$tracker_info = $trklib->get_tracker_options($tracker['trackerId']);
		if ($tracker_info['adminOnlyViewEditItem'] === 'y') {
			$tracker = null;
		}
	}

}
$trackers['data'] = array_filter($trackers['data']);

$smarty->assign_by_ref('cant', $trackers['cant']);
$smarty->assign_by_ref('trackers', $trackers["data"]);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

include_once ('tiki-section_options.php');

// Display the template
$smarty->display("tiki-list_trackers.tpl");
