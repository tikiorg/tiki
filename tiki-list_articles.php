<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_articles.php,v 1.17 2004-03-31 07:38:41 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

if ($feature_articles != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

/*
if($feature_listPages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/

/*
// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display("error.tpl");
  die;  
}
*/
if (isset($_REQUEST["remove"])) {

	if ($tiki_p_remove_article != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot remove articles"));

		$smarty->display("error.tpl");
		die;
	}
  $area = 'delarticle';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$artlib->remove_article($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
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

$now = date("U");
if( ($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y') ) {
  $pdate = '';
} elseif(isset($_SESSION["thedate"])) {
  if($_SESSION["thedate"]<$now) {
    // If the session is older then set it to today
    // so you can list articles
    $pdate = $now; 
  } else {
      $pdate = $_SESSION["thedate"]; 
  }
} else {
	$pdate = $now;
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (!isset($_REQUEST["type"])) {
	$_REQUEST["type"] = '';
}

if (!isset($_REQUEST["topic"])) {
	$_REQUEST["topic"] = '';
}

$smarty->assign('find_topic', $_REQUEST["topic"]);
$smarty->assign('find_type', $_REQUEST["type"]);

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_articles($offset, $maxRecords, $sort_mode, $find, $pdate, $user, $_REQUEST["type"], $_REQUEST["topic"]);
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
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);

$section = 'cms';
include_once ('tiki-section_options.php');

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $listpages["cant"]);
}
ask_ticket('list-articles');

// Display the template
$smarty->assign('mid', 'tiki-list_articles.tpl');
$smarty->display("tiki.tpl");

?>
