<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-lastchanges.php,v 1.13 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/histlib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($feature_lastChanges != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_lastChanges");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions (depends on permissions for wiki pages)
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display("error.tpl");
  die;  
}

// lines added by ramiro_v on 11/03/2002 begins here
// if there is no request to find something, look for everything :)
if (!isset($_REQUEST["find"])) {
	$findwhat = '';
} else {
	$findwhat = $_REQUEST["find"];
}

$smarty->assign('find', $findwhat);

// lines added by ramiro_v on 11/03/2002 ends here

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["days"])) {
	$days = 1;
} else {
	$days = $_REQUEST["days"];
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('days', $days);
// next line added by ramiro_v on 11/03/2002 
$smarty->assign_by_ref('findwhat', $findwhat);
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

// Get a list of last changes to the Wiki database
$more = 0;
// the following line has been modified by ramiro_v on 11/03/2002
$lastchanges = $histlib->get_last_changes($days, $offset, $maxRecords, $sort_mode, $findwhat);
// next line added by ramiro_v on 11/03/2002 
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
$smarty->assign('mid', 'tiki-lastchanges.tpl');
$smarty->display("tiki.tpl");

?>
