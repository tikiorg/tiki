<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_student_last_changes.php,v 1.5 2004-03-19 18:09:59 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Adapted from tiki-lastchanges.php

// Todo:
// Lots of code cleanup.
// tiki-hw_student_last_changes.tpl still isn't quite right.
// The works should apear in reverse cronological order!

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/homework/homeworklib.php');
$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

// Now check permissions
if($tiki_p_hw_student != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display("error.tpl");
  die;  
}

// if there is no request to find something, look for everything :)
if (!isset($_REQUEST["find"])) {
  $findwhat = '';
} else {
  $findwhat = $_REQUEST["find"];
}
$smarty->assign('find', $findwhat);

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 0 for "All changes"
if (!isset($_REQUEST["days"])) {
	$days = 0;
} else {
	$days = $_REQUEST["days"];
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('days', $days);
$smarty->assign_by_ref('findwhat', $findwhat);
$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it, if not then use offset = 0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

// This isn't quite right.
// I want it to work like the Last changes module, which shows only
//  one entry for each page that was changed.
//  I don't want it to work like the wiki last changes menu item which 
//  show every version that was saved.
//  Have to check that rollbacks have updated time stamps.

// Get a list of last changes to the hw database
$more = 0;
$lastchanges = $homeworklib->hw_pages_list(0, -1,'lastModif_desc');

$smarty->assign_by_ref('cant_records', $lastchanges["cant"]);

// If there're more records then assign next_offset
$cant_pages = ceil($lastchanges["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($lastchanges["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('lastchanges', $lastchanges["data"]);
ask_ticket('lastchanges');

// Display the template
$smarty->assign('mid', 'tiki-hw_student_last_changes.tpl');
$smarty->display("tiki.tpl");

?>
