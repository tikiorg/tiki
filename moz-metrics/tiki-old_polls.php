<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-old_polls.php,v 1.14.2.2 2007-12-06 14:14:11 nkoth Exp $
require_once ('tiki-setup.php');
include_once ('lib/polls/polllib.php');
if (!isset($polllib)) {
	$polllib = new PollLib;
}
if ($prefs['feature_polls'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_polls");
	$smarty->display("error.tpl");
	die;
}
// Now check permissions to access this page
if ($tiki_p_view_poll_results != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied. You cannot view this page."));
	$smarty->display("error.tpl");
	die;
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
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
// Get a list of last changes to the Wiki database
$listpages = $polllib->list_all_polls($offset, $maxRecords, $sort_mode, $find);
// If there're more records then assign next_offset
$smarty->assign_by_ref('cant_pages', $listpages["cant"]);
$smarty->assign_by_ref('listpages', $listpages["data"]);
// Display the template
$smarty->assign('mid', 'tiki-old_polls.tpl');
$smarty->display("tiki.tpl");
