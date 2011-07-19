<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/articles/artlib.php');
$access->check_feature('feature_submissions');
if (isset($_REQUEST["remove"])) {
	$access->check_permission('tiki_p_remove_submission');
	$access->check_authenticity();
	$artlib->remove_submission($_REQUEST["remove"]);
}
if (isset($_REQUEST["approve"])) {
	check_ticket('list-submissions');
	$access->check_permission('tiki_p_approve_submission');
	$artlib->approve_submission($_REQUEST["approve"]);
}
// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'publishDate_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y')) {
	$pdate = '';
} elseif (isset($_SESSION["thedate"])) {
	if ($_SESSION["thedate"] < $tikilib->now) {
		$pdate = $_SESSION["thedate"];
	} else {
		$pdate = $tikilib->now;
	}
} else {
	$pdate = $tikilib->now;
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$listpages = $artlib->list_submissions($offset, $maxRecords, $sort_mode, $find, $pdate);
$smarty->assign_by_ref('cant_pages', $listpages["cant"]);
include_once ('tiki-section_options.php');
$smarty->assign_by_ref('listpages', $listpages["data"]);
ask_ticket('list-submissions');
// Display the template
$smarty->assign('mid', 'tiki-list_submissions.tpl');
$smarty->display("tiki.tpl");
