<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_programmed_content.php,v 1.24 2007-10-12 07:55:26 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/dcs/dcslib.php');

if (!isset($dcslib)) {
	$dcslib = new DCSLib($dbTiki);
}

if ($prefs['feature_dynamic_content'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_dynamic_content");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_dynamic != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

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
  $area = 'deldyncontent';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$dcslib->remove_programmed_content($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

$smarty->assign('data', '');
$smarty->assign('publishDate', $tikilib->now);
$smarty->assign('actual', '');

if (isset($_REQUEST["save"])) {
	check_ticket('edit-programmed-content');
	$publishDate = TikiLib::make_time($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"],
																   0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);

	$id = $dcslib->replace_programmed_content($_REQUEST["pId"], $_REQUEST["contentId"], $publishDate, $_REQUEST["data"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('publishDate', $publishDate);
	$smarty->assign('pId', $id);
}

if (isset($_REQUEST["edit"])) {
	$info = $dcslib->get_programmed_content($_REQUEST["edit"]);

	$actual = $dcslib->get_actual_content_date($_REQUEST["contentId"]);
	$smarty->assign('actual', $actual);
	$smarty->assign('data', $info["data"]);
	$smarty->assign('publishDate', $info["publishDate"]);
	$smarty->assign('pId', $info["pId"]);
}

$actual = $dcslib->get_actual_content_date($_REQUEST["contentId"]);
$smarty->assign('actual', $actual);

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
$listpages = $dcslib->list_programmed_content($_REQUEST["contentId"], $offset, $maxRecords, $sort_mode, $find);

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listpages["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
ask_ticket('edit-programmed-content');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_programmed_content.tpl');
$smarty->display("tiki.tpl");

?>
