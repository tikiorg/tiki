<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-list_articles.php,v 1.38.2.2 2008-03-21 23:06:51 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'cms';
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

$smarty->assign('headtitle',tra('List Articles'));

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if($tiki_p_read_article != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra("Permission denied you cannot view pages"));
	$smarty->display("error.tpl");
	die;  
}

if (isset($_REQUEST["remove"])) {

	if ($tiki_p_remove_article != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied you cannot remove articles"));

		$smarty->display("error.tpl");
		die;
	}
  $area = 'delarticle';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
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
if (!empty($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
} else {
	$maxRecords = $maxRecords;
}
#samrty->assign_by_ref('maxRecords', $maxRecords);

if( ($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y') ) {
  $pdate = '';
} elseif(isset($_SESSION["thedate"])) {
  if($_SESSION["thedate"]<$tikilib->now) {
    // If the session is older then set it to today
    // so you can list articles
    $pdate = $tikilib->now; 
  } else {
      $pdate = $_SESSION["thedate"]; 
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

if (!isset($_REQUEST["type"])) {
	$_REQUEST["type"] = '';
}

if (!isset($_REQUEST["topic"])) {
	$_REQUEST["topic"] = '';
}

if (!isset($_REQUEST["categId"])) {
	$_REQUEST["categId"] = '';
}
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}
$smarty->assign('find_topic', $_REQUEST["topic"]);
$smarty->assign('find_type', $_REQUEST["type"]);
$smarty->assign('find_categId', $_REQUEST["categId"]);
$smarty->assign('find_lang', $_REQUEST['lang']);



$visible_only='y';
if( ($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y') ) { $visible_only="n"; }

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_articles($offset, $maxRecords, $sort_mode, $find, $pdate, $user, $_REQUEST["type"], $_REQUEST["topic"], $visible_only, '', $_REQUEST["categId"], '', '', $_REQUEST['lang']);
// If there're more records then assign next_offset
$smarty->assign_by_ref('cant', $listpages['cant']);

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once ('lib/categories/categlib.php');
	$categories = $categlib->get_all_categories_ext();
	$smarty->assign_by_ref('categories', $categories);
}
if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages(false, 'y');
	$smarty->assign_by_ref('languages', $languages);
}

if ($tiki_p_edit_article != 'y' && $tiki_p_remove_article != 'y') { //check one editable
	foreach ($listpages['data'] as $page) {
		if ($page['author'] == $user && $page['creator_edit'] == 'y') {
			$smarty->assign('oneEditPage', 'y');
			break;
		}
	}
}

include_once ('tiki-section_options.php');

if ($prefs['feature_mobile'] =='y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $listpages["cant"]);
}
ask_ticket('list-articles');

// Display the template
$smarty->assign('mid', 'tiki-list_articles.tpl');
$smarty->display("tiki.tpl");

?>
