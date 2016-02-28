<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$auto_query_args = array('contentId','sort_mode','offset','find');

$access->check_feature('feature_dynamic_content');
$access->check_permission('tiki_p_admin_dynamic');

$dcslib = TikiLib::lib('dcs');

if (!isset($_REQUEST["contentId"])) {
	$smarty->assign('msg', tra("No content id indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('contentId', $_REQUEST["contentId"]);
$smarty->assign('pId', 0);
$info = $dcslib->get_content($_REQUEST["contentId"]);
$smarty->assign('description', $info["description"]);

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$dcslib->remove_programmed_content($_REQUEST["remove"]);
}

$smarty->assign('data', '');
$smarty->assign('publishDate', $tikilib->now);
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
$userprefslib = TikiLib::lib('userprefs');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

$smarty->assign('actual', '');

if (isset($_REQUEST["save"])) {
	check_ticket('edit-programmed-content');

	if ( $_REQUEST['content_type'] == 'page' ) {
		$content = 'page:' . $_REQUEST['page_name'];
	} else {
		$content = $_REQUEST['data'];
	}

	if (!empty($_REQUEST['Time_Meridian'])) {
		$_REQUEST['Time_Hour'] = date('H', strtotime($_REQUEST['Time_Hour'] . ':00 ' . $_REQUEST['Time_Meridian']));
	}
	$publishDate = TikiLib::make_time(
		$_REQUEST["Time_Hour"],
		$_REQUEST["Time_Minute"],
		0,
		$_REQUEST["Date_Month"],
		$_REQUEST["Date_Day"],
		$_REQUEST["Date_Year"]
	);

	$id = $dcslib->replace_programmed_content($_REQUEST["pId"], $_REQUEST["contentId"], $publishDate, $content, $_REQUEST['content_type']);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('publishDate', $publishDate);
	$smarty->assign('pId', $id);

	$_REQUEST['edit'] = $id;
}

if (isset($_REQUEST["edit"])) {
	$info = $dcslib->get_programmed_content($_REQUEST["edit"]);

	$actual = $dcslib->get_actual_content_date($_REQUEST["contentId"]);
	$smarty->assign('info', $info);
	$smarty->assign('actual', $actual);
	$smarty->assign('data', $info["data"]);
	$smarty->assign('publishDate', $info["publishDate"]);
	$smarty->assign('pId', $info["pId"]);
}

$actual = $dcslib->get_actual_content_date($_REQUEST["contentId"]);
$smarty->assign('actual', $actual);

// This script can receive the threshold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'publishDate_desc';
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

// Get a list of last changes to the Wiki database
$listpages = $dcslib->list_programmed_content($_REQUEST["contentId"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant', $listpages["cant"]);
$smarty->assign_by_ref('listpages', $listpages["data"]);

ask_ticket('edit-programmed-content');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_programmed_content.tpl');
$smarty->display("tiki.tpl");
