<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/articles/artlib.php');
$smarty->assign('headtitle', tra('List Articles'));
$access->check_feature('feature_articles');
$access->check_permission('tiki_p_read_article');
$auto_query_args = array('sort_mode', 'category', 'offset', 'maxRecords', 'find', 'find_from_Month', 'find_from_Day', 'find_from_Year', 'find_to_Month', 'find_to_Day', 'find_to_Year', 'type', 'topic', 'cat_categories', 'categId', 'lang', 'mode', 'mapview', 'searchmap', 'searchlist');
if ($prefs["gmap_article_list"] == 'y') {
	$smarty->assign('gmapbuttons', true);
} else {
	$smarty->assign('gmapbuttons', false);
}
if (isset($_REQUEST["mapview"]) && $_REQUEST["mapview"] == 'y' && !isset($_REQUEST["searchmap"]) && !isset($_REQUEST["searchlist"]) || isset($_REQUEST["searchmap"]) && !isset($_REQUEST["searchlist"])) {
	$smarty->assign('mapview', true);
}
if (isset($_REQUEST["mapview"]) && $_REQUEST["mapview"] == 'n' && !isset($_REQUEST["searchmap"]) && !isset($_REQUEST["searchlist"]) || isset($_REQUEST["searchlist"]) && !isset($_REQUEST["searchmap"]) ) {
	$smarty->assign('mapview', false);
}
if (isset($_REQUEST["remove"])) {
	$artperms = Perms::get( array( 'type' => 'article', 'object' => $_REQUEST['remove'] ) );

	if ($artperms->remove_article != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to remove articles"));
		$smarty->display("error.tpl");
		die;
	}
	$access->check_authenticity();
	$artlib->remove_article($_REQUEST["remove"]);
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
$smarty->assign_by_ref('maxRecords', $maxRecords);

$visible_only = 'y';
if (($tiki_p_admin == 'y') || ($tiki_p_admin_cms == 'y')) {
	$date_max = '';
	$visible_only = "n";
} elseif (isset($_SESSION["thedate"])) {
	if ($_SESSION["thedate"] < $tikilib->now) {
		// If the session is older then set it to today
		// so you can list articles
		$date_max = $tikilib->now;
	} else {
		$date_max = $_SESSION["thedate"];
	}
} else {
	$date_max = $tikilib->now;
}
if (!empty($_REQUEST["find_from_Month"]) && !empty($_REQUEST["find_from_Day"]) && !empty($_REQUEST["find_from_Year"])) {
	$date_min = $tikilib->make_time(0, 0, 0, $_REQUEST["find_from_Month"], $_REQUEST["find_from_Day"], $_REQUEST["find_from_Year"]);
	$smarty->assign('find_date_from', $date_min);
} else {
	$date_min = 0;
	$smarty->assign('find_date_from', $tikilib->now - 365*24*3600);
}
if (isset($_REQUEST["find_to_Month"]) && isset($_REQUEST["find_to_Day"]) && isset($_REQUEST["find_to_Year"])) {
	$t_date_max = $tikilib->make_time(23, 59, 59, $_REQUEST["find_to_Month"], $_REQUEST["find_to_Day"], $_REQUEST["find_to_Year"]);
	if ($t_date_max < $date_max || $date_max == '') {
		$date_max = $t_date_max;
		$visible_only = 'y';
	}
}
$smarty->assign('find_date_to', $date_max);
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

$filter['categId'] = 0;
if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['cat_categories'])) {
	$filter['categId'] = $_REQUEST['cat_categories'];
	if (count($_REQUEST['cat_categories']) > 1) {
		$smarty->assign('find_cat_categories', $_REQUEST['cat_categories']);
		unset($_REQUEST['categId']);
	} else {
		$_REQUEST['categId'] = $_REQUEST['cat_categories'][0];
		unset($_REQUEST['cat_categories']);
	}
} else {
		$_REQUEST['cat_categories'] = array();
}
if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['categId'])) {
	$filter['categId'] = $_REQUEST['categId'];
	$smarty->assign('find_categId', $_REQUEST['categId']);
}
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}
$smarty->assign('find_topic', $_REQUEST["topic"]);
$smarty->assign('find_type', $_REQUEST["type"]);
$smarty->assign('find_lang', $_REQUEST['lang']);
// Get a list of last changes to the Wiki database
$listpages = $artlib->list_articles($offset, $maxRecords, $sort_mode, $find, $date_min, $date_max, $user, $_REQUEST["type"], $_REQUEST["topic"], $visible_only, '', $filter["categId"], '', '', $_REQUEST['lang']);
// If there're more records then assign next_offset
$smarty->assign_by_ref('cant', $listpages['cant']);
$smarty->assign_by_ref('listpages', $listpages["data"]);

if ($prefs["gmap_article_list"] == 'y') {
	// Generate Google map plugin data
	global $gmapobjectarray;
	$gmapobjectarray = array();
	foreach ($listpages["data"] as $art) {
		$gmapobjectarray[] = array('type' => 'article',
			'id' => $art["articleId"],
			'title' => $art["title"],
			'href' => 'tiki-read_article.php?articleId=' . $art["articleId"],
		);
	}
}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	include_once ('lib/categories/categlib.php');
	$categories = $categlib->get_all_categories_respect_perms(null, 'view_category');
	$smarty->assign_by_ref('categories', $categories);
	$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories, true, $_REQUEST['cat_categories']));	
}
if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages(false, 'y');
	$smarty->assign_by_ref('languages', $languages);
}
if ($tiki_p_edit_article != 'y' && $tiki_p_remove_article != 'y') { //check one editable
	foreach($listpages['data'] as $page) {
		if ($page['author'] == $user && $page['creator_edit'] == 'y') {
			$smarty->assign('oneEditPage', 'y');
			break;
		}
	}
}
include_once ('tiki-section_options.php');
if ($prefs['feature_mobile'] == 'y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");
	HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $listpages["cant"]);
}
ask_ticket('list-articles');
// Display the template
$smarty->assign('mid', 'tiki-list_articles.tpl');
$smarty->display("tiki.tpl");
