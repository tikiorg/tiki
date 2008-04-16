<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-view_articles.php,v 1.41.2.1 2007-11-08 21:47:51 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'cms';
require_once ('tiki-setup.php');

$smarty->assign('headtitle',tra('Articles'));

include_once ('lib/articles/artlib.php');
include_once("lib/commentslib.php");
if ($prefs['feature_categories'] == 'y') {
	include_once('lib/categories/categlib.php');
}

$commentslib = new Comments($dbTiki);

/*
if($prefs['feature_listPages'] != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/
if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_read_article != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
	if ($tiki_p_remove_article != 'y') {
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

if (isset($_SESSION["thedate"])) {
	if ($_SESSION["thedate"] < $tikilib->now) {
		$pdate = $_SESSION["thedate"];
	} else {
		if ($tiki_p_admin == 'y' || $tiki_p_admin_cms == 'y') {
			$pdate = $_SESSION["thedate"];
		} else {
			$pdate = $tikilib->now;
		}
	}
} else {
	$pdate = $tikilib->now;
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('find', $find);

if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = '';
}
$smarty->assign_by_ref('type', $type);

if (isset($_REQUEST["topic"])) {
	$topic = $_REQUEST["topic"];
 } else {
	$topic = '';
}
if (isset($REQUEST['topicName'])) {
	$topicName = $REQUEST['topicName'];
 } else {
	$topicName = '';
 }
$smarty->assign_by_ref('topic', $topic);

if (isset($_REQUEST["categId"])) {
	$categId = $_REQUEST["categId"];
} else {
	$categId = '';
}
$smarty->assign_by_ref('categId', $categId);

if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_articles($offset, $prefs['maxArticles'], $sort_mode, $find, $pdate, $user, $type, $topic, 'y', $topicName, $categId, '', '', $_REQUEST['lang']);
if ($prefs['feature_multilingual'] == 'y') {
	include_once("lib/multilingual/multilinguallib.php");
	$listpages['data'] = $multilinguallib->selectLangList('article', $listpages['data']);
}

$temp_max = count($listpages["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data($listpages["data"][$i]["heading"]);
	$comments_prefix_var='article:';
	$comments_object_var=$listpages["data"][$i]["articleId"];
	$comments_objectId = $comments_prefix_var.$comments_object_var;
	$listpages["data"][$i]["comments_cant"] = $commentslib->count_comments($comments_objectId);
}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

$cant_pages = ceil($listpages["cant"] / $prefs['maxArticles']);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $prefs['maxArticles']));
$smarty->assign('maxArticles', $prefs['maxArticles']);

if ($listpages["cant"] > ($offset + $prefs['maxArticles'])) {
	$smarty->assign('next_offset', $offset + $prefs['maxArticles']);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $prefs['maxArticles']);
} else {
	$smarty->assign('prev_offset', -1);
}
// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages', $listpages["data"]);
$smarty->assign_by_ref('cant', $listpages["cant"]);

include_once ('tiki-section_options.php');

ask_ticket('view_article');

// Display the template
$smarty->assign('mid', 'tiki-view_articles.tpl');
$smarty->display("tiki.tpl");

?>
