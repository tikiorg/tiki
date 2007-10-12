<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_submissions.php,v 1.21 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

/*
if($prefs['feature_listPages'] != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/
if ($prefs['feature_submissions'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_submissions");

	$smarty->display("error.tpl");
	die;
}

/*
// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display("error.tpl");
  die;  
}
*/
if (isset($_REQUEST["remove"])) {
	if ($tiki_p_remove_submission != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot remove submissions"));
		$smarty->display("error.tpl");
		die;
	}
  $area = 'delsubmission';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$artlib->remove_submission($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["approve"])) {
	check_ticket('list-submissions');
	if ($tiki_p_approve_submission != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot approve submissions"));

		$smarty->display("error.tpl");
		die;
	}

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

if( ($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y') ) {
  $pdate = '';
} elseif(isset($_SESSION["thedate"])) {
  if($_SESSION["thedate"]<$tikilib->now) {
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

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_submissions($offset, $maxRecords, $sort_mode, $find, $pdate);
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

$section = 'cms';
include_once ('tiki-section_options.php');

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
ask_ticket('list-submissions');

// Display the template
$smarty->assign('mid', 'tiki-list_submissions.tpl');
$smarty->display("tiki.tpl");

?>
